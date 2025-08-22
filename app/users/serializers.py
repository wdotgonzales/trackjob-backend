from rest_framework import serializers
from .models import User, VerificationCode

from rest_framework_simplejwt.serializers import TokenObtainPairSerializer
from rest_framework.exceptions import AuthenticationFailed
from django.contrib.auth import authenticate

from django.utils import timezone


# Serializer for registering a user with password confirmation
class UserSerializer(serializers.ModelSerializer):
    repeat_password = serializers.CharField(write_only=True)

    class Meta:
        model = User
        fields = ['id', 'email', 'full_name', 'password', 'repeat_password', 'profile_url']
        extra_kwargs = {
            'password': {'write_only': True}  # Don't return password in API responses
        }

    # Custom validation for passoword
    def validate(self, data):
        password = data['password']
        repeat_password = data['repeat_password']

        # Check if passwords match
        if password != repeat_password:
            raise serializers.ValidationError("Passwords do not match.")

        # Check minimum length
        if len(password) < 8:
            raise serializers.ValidationError("Password must be at least 8 characters long.")

        # Check for whitespace
        if any(char.isspace() for char in password):
            raise serializers.ValidationError("Password must not contain any spaces.")

        return data

    # Override create to hash password and remove repeat_password
    def create(self, validated_data):
        validated_data.pop('repeat_password')       # Remove repeat_password before saving
        password = validated_data.pop('password')   # Extract password
        user = User(**validated_data)
        user.set_password(password)                 # Hash the password
        user.save()
        return user
    
# Serializer for custom token obtain pair
class CustomTokenObtainPairSerializer(TokenObtainPairSerializer):
    # Override the method to include email in the token
    def get_token(cls, user):
        # Update last login time
        user.last_login = timezone.now()
        user.save(update_fields=['last_login'])
        
        token = super().get_token(user)
        token['email'] = user.email
        return token
    
    # Override the validate method to authenticate user
    def validate(self, attrs):
        email = attrs.get("email")
        password = attrs.get("password")

        user = authenticate(username=email, password=password)
        self.authenticated_user = user  
        if not user:
            return {}

        return super().validate(attrs)


class VerificationCodeSerializer(serializers.ModelSerializer):
    """
    Serializer for email verification codes.
    
    Handles serialization/deserialization of verification codes sent 
    during user registration process.
    """
    
    class Meta:
        model = VerificationCode
        fields = ['email', 'code', 'created_at', 'expires_at']
        
        
class ResetPasswordSerializer(serializers.Serializer):
    """
    Serializer for password reset functionality.
    
    Validates password reset data and updates user password.
    """
    email = serializers.EmailField()
    new_password = serializers.CharField(write_only=True)
    confirm_password = serializers.CharField(write_only=True)

    def validate(self, attrs):
        """
        Validate password reset data.
        
        Checks password requirements:
        - Minimum 8 characters
        - No spaces
        - Passwords match
        
        Returns:
            dict: Validated attributes
            
        Raises:
            ValidationError: If validation fails
        """
        new_password = attrs.get('new_password')
        confirm_password = attrs.get('confirm_password')
        email = attrs.get('email')
        
        # Check for required fields
        if not new_password:
            raise serializers.ValidationError('New password is required.')
        
        if not confirm_password:
            raise serializers.ValidationError('Password confirmation is required.')
        
        if not email:
            raise serializers.ValidationError('Email is required.')

        # Check if passwords match
        if new_password != confirm_password:
            raise serializers.ValidationError("Passwords do not match.")

        # Check minimum length
        if len(new_password) < 8:
            raise serializers.ValidationError("Password must be at least 8 characters long.")

        # Check for whitespace
        if any(char.isspace() for char in new_password):
            raise serializers.ValidationError("Password must not contain any spaces.")
        
        # Check if user exists
        if not User.objects.filter(email=email).exists():
            raise serializers.ValidationError("User with this email does not exist.")

        return attrs

    def save(self, **kwargs):
        """
        Update user password with validated data.
        
        Note: User existence already validated in validate() method
        """
        email = self.validated_data['email']
        new_password = self.validated_data['new_password']

        user = User.objects.get(email=email)
        user.set_password(new_password) 
        user.save()


class ChangeProfileUrlSerializer(serializers.ModelSerializer):
    class Meta:
        model = User
        fields = ['profile_url']

    def validate(self, attrs):
        profile_url = attrs.get('profile_url', '').strip()

        if profile_url == '':
            raise serializers.ValidationError("Profile URL cannot be empty.")

        if not profile_url.startswith(('http://', 'https://')):
            raise serializers.ValidationError("Profile URL must start with 'http://' or 'https://'.")

        return attrs


class EmailOnlyTokenObtainPairSerializer(TokenObtainPairSerializer):
    username_field = 'email'
    
    def __init__(self, *args, **kwargs):
        super().__init__(*args, **kwargs)
        # Remove password field
        self.fields.pop('password', None)
        # Keep only email field
        self.fields['email'] = serializers.EmailField()
    
    def validate(self, attrs):
        email = attrs.get('email')
        
        if not email:
            raise serializers.ValidationError('Email is required')
        
        try:
            user = User.objects.get(email=email)
        except User.DoesNotExist:
            raise serializers.ValidationError('User with this email does not exist')
        
        # Skip password validation, directly generate tokens
        refresh = self.get_token(user)
        
        self.authenticated_user = user
        
        return {
            'refresh': str(refresh),
            'access': str(refresh.access_token),
        }
    
    
class CheckEmailExistenceSerializer(serializers.ModelSerializer):
    class Meta:
        model = User
        fields = ['email']
    
    def validate(self, attrs):
        email = attrs.get('email')
        if not email:
            raise serializers.ValidationError('Email is required')
        
        try:
            user = User.objects.get(email=email)
            # If we reach this line, the email exists
            raise serializers.ValidationError('Email already exists')
        except User.DoesNotExist:
            # Email doesn't exist, which is what we want for registration
            pass
        
        return attrs

class UpdateProfileSerializer(serializers.ModelSerializer):
    """
    Serializer for updating user profile information.
    
    Allows updating full_name and profile_url fields.
    """
    class Meta:
        model = User
        fields = ['full_name', 'profile_url']
    
    def validate(self, attrs):
        """
        Validate profile update data.
        
        Args:
            attrs: Dictionary containing field values to update
            
        Returns:
            dict: Validated attributes
            
        Raises:
            ValidationError: If validation fails
        """
        full_name = attrs.get('full_name', '').strip() if attrs.get('full_name') else None
        profile_url = attrs.get('profile_url', '').strip() if attrs.get('profile_url') else None
        
        # Validate full_name if provided
        if full_name is not None:
            if len(full_name) == 0:
                raise serializers.ValidationError("Full name cannot be empty.")
            if len(full_name) > 255:
                raise serializers.ValidationError("Full name cannot exceed 255 characters.")
        
        # Validate profile_url if provided
        if profile_url is not None:
            if len(profile_url) == 0:
                # Allow empty string to clear the profile URL
                attrs['profile_url'] = None
            elif not profile_url.startswith(('http://', 'https://')):
                raise serializers.ValidationError("Profile URL must start with 'http://' or 'https://'.")
        
        return attrs

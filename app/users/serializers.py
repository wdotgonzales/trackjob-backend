from rest_framework import serializers
from .models import User, VerificationCode

from rest_framework_simplejwt.serializers import TokenObtainPairSerializer
from rest_framework.exceptions import AuthenticationFailed
from django.contrib.auth import authenticate

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

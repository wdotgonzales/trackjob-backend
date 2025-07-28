from rest_framework import serializers
from .models import User

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

from django.shortcuts import render
from .models import User, VerificationCode
from .serializers import UserSerializer, ResetPasswordSerializer, ChangeProfileUrlSerializer
from rest_framework.viewsets import ViewSet
from rest_framework import status
from .responses import CustomResponse 

from rest_framework_simplejwt.views import TokenObtainPairView
from .serializers import CustomTokenObtainPairSerializer, VerificationCodeSerializer

import jwt
from rest_framework_simplejwt.settings import api_settings
from rest_framework.views import APIView

from rest_framework.permissions import AllowAny

from django.core.mail import EmailMultiAlternatives
from django.template.loader import render_to_string

from django.utils import timezone
from datetime import timedelta
import random


# UserViewSet handles user-related operations
class UserViewSet(ViewSet):
    """ViewSet for user-related operations."""
    
    def list(self, request):
        """
        Retrieve all users.
        
        Returns:
            CustomResponse: List of all users with success message
        """
        users = User.objects.all()
        serializer = UserSerializer(users, many=True)
        return CustomResponse(
            data=serializer.data,
            message="Users retrieved successfully",
            status_code=status.HTTP_200_OK                  
        )


# RegisterViewSet handles user registration
class RegisterViewSet(ViewSet):
    """ViewSet for user registration flow including email verification."""
    permission_classes = [AllowAny]
    
    def send_verification_code(self, request):
        """
        Send 6-digit verification code to email.
        
        Args:
            request: Contains 'email' field
            
        Returns:
            CustomResponse: Success message or error if email exists/invalid
        """
        to_email = request.data.get("email")

        if not to_email:
            return CustomResponse(
                data=None,
                message="Email is required.",
                status_code=status.HTTP_400_BAD_REQUEST
            )
            
        if User.objects.filter(email=to_email).exists():
            return CustomResponse(
                data=None,
                message="Email is already taken.",
                status_code=status.HTTP_400_BAD_REQUEST
            )

        # Generate 6-digit code    
        code = f"{random.randint(100000, 999999)}"

        subject = f"TrackJob Verification Code"
        context = {
            "user": {"email": to_email, "code": code}
        }

        text_content = render_to_string("users/emails/welcome.txt", context)
        html_content = render_to_string("users/emails/welcome.html", context)

        msg = EmailMultiAlternatives(
            subject=subject,
            body=text_content,
            from_email=None,  # uses DEFAULT_FROM_EMAIL
            to=[to_email]
        )
        msg.attach_alternative(html_content, "text/html")
        msg.send(fail_silently=False)
        
        # Code expires in 5 minutes
        created_at = timezone.now()
        expires_at = created_at + timedelta(minutes=5)
        
        verification_code_data = {
            "email": to_email,
            "code": code,
            "created_at": created_at,
            "expires_at": expires_at
        }
        
        serializer = VerificationCodeSerializer(data=verification_code_data)
        serializer.is_valid(raise_exception=True)
        serializer.save()

        return CustomResponse(
            data={"email": to_email},
            message="Verification code sent to email successfully.",
            status_code=status.HTTP_200_OK
        )
    
    def verify_code(self, request):
        """
        Verify email code and delete it if valid.
        
        Args:
            request: Contains 'email' and 'code' fields
            
        Returns:
            CustomResponse: Success if valid, error if invalid/expired
        """
        code = request.data.get("code")
        email = request.data.get("email")

        if not code or not email:
            return CustomResponse(
                data=None,
                message="Email and verification code are required.",
                status_code=status.HTTP_400_BAD_REQUEST
            )

        try:
            verification = VerificationCode.objects.get(code=code, email=email)
        except VerificationCode.DoesNotExist:
            return CustomResponse(
                data=None,
                message="Invalid verification code.",
                status_code=status.HTTP_404_NOT_FOUND
            )

        if verification.expires_at < timezone.now():
            return CustomResponse(
                data=None,
                message="Verification code has expired.",
                status_code=status.HTTP_410_GONE
            )

        # Delete code after successful verification
        verification.delete()   

        return CustomResponse(
            data={"email": email},
            message="Verification code is valid.",
            status_code=status.HTTP_200_OK
        )
    
    def register(self, request):
        """
        Create new user account.
        
        Args:
            request: User data (email, password, etc.)
            
        Returns:
            CustomResponse: User data if successful, validation error otherwise
        """
        serializer = UserSerializer(data=request.data)  
        if serializer.is_valid():
            user = serializer.save()
            return CustomResponse(
                data=UserSerializer(user).data,
                message="User registered successfully",
                status_code=status.HTTP_201_CREATED
            )

        first_error_message = next(iter(serializer.errors.values()))[0]

        return CustomResponse(
            data={},
            message=first_error_message,
            status_code=status.HTTP_400_BAD_REQUEST
        )


# CustomTokenObtainPairView handles token generation        
class CustomTokenObtainPairView(TokenObtainPairView):
    """Custom JWT token generation with formatted responses."""
    permission_classes = [AllowAny]
    serializer_class = CustomTokenObtainPairSerializer

    def post(self, request, *args, **kwargs):
        """
        Authenticate user and return JWT tokens.
        
        Args:
            request: Contains 'email' and 'password'
            
        Returns:
            CustomResponse: Access/refresh tokens if valid, error otherwise
        """
        serializer = self.get_serializer(data=request.data)

        if not serializer.is_valid():
            return CustomResponse(
                data={},
                message="Invalid email or password",
                status_code=status.HTTP_401_UNAUTHORIZED
            )

        if not getattr(serializer, "authenticated_user", None):
            return CustomResponse(
                data={},
                message="Invalid email or password",
                status_code=status.HTTP_401_UNAUTHORIZED
            )

        return CustomResponse(
            data=serializer.validated_data,
            message="Login successful",
            status_code=status.HTTP_200_OK
        )
       

# DecodeTokenView handles decoding of JWT tokens 
class DecodeTokenView(APIView):
    """JWT token validation and decoding."""
    permission_classes = [AllowAny]
    
    def post(self, request):
        """
        Decode and validate JWT token.
        
        Args:
            request: Contains 'token' field
            
        Returns:
            CustomResponse: Decoded token payload or error message
        """
        token = request.data.get('token')
        if not token:
            return CustomResponse(
                data={},
                message="Token is required",
                status_code=status.HTTP_400_BAD_REQUEST
            )

        try:
            decoded = jwt.decode(
                token,
                key=api_settings.SIGNING_KEY,
                algorithms=[api_settings.ALGORITHM]
            )
            return CustomResponse(
                data=decoded,
                message="Token decoded successfully",
                status_code=status.HTTP_200_OK
            )
        except jwt.ExpiredSignatureError:
            return CustomResponse(
                data={},
                message="Token has expired",
                status_code=status.HTTP_401_UNAUTHORIZED
            )
        except jwt.InvalidTokenError as e:
            return CustomResponse(
                data={},
                message="Invalid token",
                status_code=status.HTTP_400_BAD_REQUEST
            )
            
class ResetPasswordView(APIView):
    """API view for resetting user passwords."""
    permission_classes = [AllowAny]

    def post(self, request):
        """
        Reset user password with new credentials.
        
        Args:
            request: Contains 'email', 'new_password', and 'confirm_password'
            
        Returns:
            CustomResponse: Success message or validation error
        """
        serializer = ResetPasswordSerializer(data=request.data)
        
        if serializer.is_valid():
            serializer.save()
            return CustomResponse(
                data={},
                message="Password has been reset successfully.",
                status_code=status.HTTP_200_OK
            )
            
        # Return first validation error
        first_error_message = next(iter(serializer.errors.values()))[0]

        return CustomResponse(
            data={},
            message=first_error_message,
            status_code=status.HTTP_400_BAD_REQUEST
        )
        
class ChangeProfileUrlView(APIView):
    def post(self, request):
        user = request.user  

        serializer = ChangeProfileUrlSerializer(user, data=request.data, partial=True)

        if serializer.is_valid():
            serializer.save()
            return CustomResponse(
                data={'profile_url': serializer.data['profile_url']},
                message="Profile URL updated successfully.",
                status_code=status.HTTP_200_OK
            )

        first_error_message = next(iter(serializer.errors.values()))[0]

        return CustomResponse(
            data={},
            message=first_error_message,
            status_code=status.HTTP_400_BAD_REQUEST
        )
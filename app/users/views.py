from django.shortcuts import render
from .models import User
from .serializers import UserSerializer
from rest_framework.viewsets import ViewSet
from rest_framework import status
from .responses import CustomResponse 

from rest_framework_simplejwt.views import TokenObtainPairView
from .serializers import CustomTokenObtainPairSerializer

import jwt
from rest_framework_simplejwt.settings import api_settings
from rest_framework.views import APIView

from rest_framework.permissions import AllowAny

# Create your views here.

# UserViewSet handles user-related operations
class UserViewSet(ViewSet):
    def list(self, request):
        users = User.objects.all()
        serializer = UserSerializer(users, many=True)
        return CustomResponse(
            data=serializer.data,
            message="Users retrieved successfully",
            status_code=status.HTTP_200_OK                  
        )

# RegisterViewSet handles user registration
class RegisterViewSet(ViewSet):
    permission_classes = [AllowAny]
        
    def register(self, request):
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
    permission_classes = [AllowAny]
    serializer_class = CustomTokenObtainPairSerializer

    def post(self, request, *args, **kwargs):
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
    permission_classes = [AllowAny]
    def post(self, request):
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

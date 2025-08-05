from django.urls import path
from .views import UserViewSet, RegisterViewSet, CustomTokenObtainPairView, DecodeTokenView, ResetPasswordView, ChangeProfileUrlView
from rest_framework_simplejwt.views import TokenRefreshView

urlpatterns = [
    path('users/', UserViewSet.as_view({'get': 'list'}), name='user-list'),
    path('users/<int:pk>/', UserViewSet.as_view({'get': 'retrieve'}), name='user-detail'),
    
    path('register/', RegisterViewSet.as_view({'post': 'register'}), name='user-register'),
    path('login/', CustomTokenObtainPairView.as_view(), name='token_obtain_pair'),
    path('decode-token/', DecodeTokenView.as_view(), name='decode_token'),
    
    path('send-verification-code/', RegisterViewSet.as_view({'post': 'send_verification_code'}), name='send-verification-code'),
    path('verify-code/',  RegisterViewSet.as_view({'post': 'verify_code'}), name='verify-code'),
    
    path('reset-password/', ResetPasswordView.as_view(), name='reset-password'),
    
    path('change-profile-url/', ChangeProfileUrlView.as_view(), name='change-profile-url')
]
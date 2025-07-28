from django.urls import path
from .views import UserViewSet, RegisterViewSet, CustomTokenObtainPairView, DecodeTokenView

from rest_framework_simplejwt.views import TokenRefreshView

urlpatterns = [
    path('users/', UserViewSet.as_view({'get': 'list'}), name='user-list'),
    
    path('register/', RegisterViewSet.as_view({'post': 'register'}), name='user-register'),
    path('login/', CustomTokenObtainPairView.as_view(), name='token_obtain_pair'),
    path('decode-token/', DecodeTokenView.as_view(), name='decode_token'),
]

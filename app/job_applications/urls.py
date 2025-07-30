from django.urls import path, include
from rest_framework.routers import DefaultRouter
from .views import JobApplicationViewSet

router = DefaultRouter()
router.register(r'job_application', JobApplicationViewSet, basename='job_application')

urlpatterns = [
    path('', include(router.urls)),
]
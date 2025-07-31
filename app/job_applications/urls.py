from django.urls import path, include
from rest_framework.routers import DefaultRouter
from rest_framework_nested import routers  # ✅ Import this

from .views import JobApplicationViewSet, ReminderViewSet

# Base router
router = DefaultRouter()
router.register(r'job_application', JobApplicationViewSet, basename='job_application')

# Nested router
job_application_router = routers.NestedDefaultRouter(router, r'job_application', lookup='job_application')
job_application_router.register(r'reminder', ReminderViewSet, basename='job_application-reminder')

urlpatterns = [
    path('', include(router.urls)),
    path('', include(job_application_router.urls)),
]

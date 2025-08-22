from django.apps import AppConfig


class JobApplicationsConfig(AppConfig):
    default_auto_field = 'django.db.models.BigAutoField'
    name = 'job_applications'
    
    def ready(self):
        from . import signals

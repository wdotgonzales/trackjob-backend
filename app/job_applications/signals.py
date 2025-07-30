from django.core.cache import cache
from django.db.models.signals import post_save, post_delete
from django.dispatch import receiver
from job_applications.models import JobApplication

@receiver([post_save, post_delete], sender=JobApplication)
def invalidate_job_application_cache(sender, instance, **kwargs):
    try:
        cache.delete_pattern("views.decorators.cache.cache_page.job_application_detail*")
        cache.delete_pattern("views.decorators.cache.cache_page.job_application_list*")
        print(f"[Signal] Cache invalidated for JobApplication {pk}")
    except Exception as e:
        print(f"Cache invalidation failed: {e}")

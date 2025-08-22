from django.core.cache import cache
from django.db.models.signals import post_save, post_delete
from django.dispatch import receiver
from job_applications.models import JobApplication

@receiver([post_save, post_delete], sender=JobApplication)
def invalidate_job_application_cache(sender, instance, **kwargs):
    """
    Smart approach: check if delete_pattern is available, otherwise fallback.
    """
    try:
        print("Clearing cache for job application list")
        
        # Check if delete_pattern method exists
        if hasattr(cache, 'delete_pattern'):
            cache.delete_pattern("*job_application_list*")
            print("Cache cleared using delete_pattern")
        else:
            print("delete_pattern not available, clearing all cache")
            cache.clear()
            
    except Exception as e:
        print(f"Error clearing cache: {e}")
        # Fallback to clearing all cache
        try:
            cache.clear()
            print("Fallback: cleared all cache")
        except Exception as fallback_error:
            print(f"Fallback also failed: {fallback_error}")
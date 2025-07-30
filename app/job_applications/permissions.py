from rest_framework.permissions import BasePermission

class IsOwner(BasePermission):
    """
    Custom permission to allow only the task owner to retrieve/update/delete it.
    """
    def has_object_permission(self, request, view, obj):
        return obj.user == request.user

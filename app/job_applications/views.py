from django.shortcuts import render
from .models import JobApplication
from .serializers import JobApplicationCreateSerializer, JobApplicationViewSerializer
from .responses import CustomResponse
from .permissions import IsOwner
from rest_framework import viewsets, status

from rest_framework.exceptions import ValidationError, PermissionDenied, NotFound

class JobApplicationViewSet(viewsets.ModelViewSet):
    """
    ViewSet for managing job applications.
    Provides CRUD operations with custom response format and user-based filtering.
    """
    
    def get_serializer_class(self):
        """Return JobApplicationCreateSerializer for CUD operations, JobApplicationViewSerializer for read operations."""
        if self.action in ['create', 'update', 'partial_update']:
            return JobApplicationCreateSerializer
        return JobApplicationViewSerializer
    
    def get_queryset(self):
        return JobApplication.objects.all()
        
    def get_permissions(self):
        """Apply IsOwner permission for retrieve, update, and delete operations."""
        if self.action in ['retrieve', 'update', 'partial_update', 'destroy']:
            return [IsOwner()]
        return super().get_permissions()

    def perform_create(self, serializer):
        """Associate the job application with the current user."""
        serializer.save(user=self.request.user)

    def create(self, request, *args, **kwargs):
        """Create a new job application with custom validation and response format."""
        serializer = self.get_serializer(data=request.data)
        if not serializer.is_valid():
            # Extract missing required fields for better error messages
            missing_fields = [
                field for field, errors in serializer.errors.items()
                if any(err.code == 'required' for err in errors)
            ]
            if missing_fields:
                return CustomResponse(
                    data=None,
                    message=f"Missing fields: {', '.join(missing_fields)}",
                    status_code=status.HTTP_400_BAD_REQUEST
                )
            return CustomResponse(
                data=None,
                message="Invalid data",
                status_code=status.HTTP_400_BAD_REQUEST
            )

        self.perform_create(serializer)
        
        # Return formatted response using view serializer
        view_serializer = JobApplicationViewSerializer(serializer.instance)
        return CustomResponse(
            data=view_serializer.data,
            message="Job Application created successfully",
            status_code=status.HTTP_201_CREATED
        )

    def retrieve(self, request, *args, **kwargs):
        """Retrieve a single job application with permission check."""
        try:
            instance = self.get_object()
            self.check_object_permissions(request, instance)
        except PermissionDenied:
            return CustomResponse(
                data={},
                message="You are not authorized to access this job application.",
                status_code=status.HTTP_403_FORBIDDEN
            )
        except NotFound:
            return CustomResponse(
                data={},
                message="Job Application not found.",
                status_code=status.HTTP_404_NOT_FOUND
            )

        serializer = self.get_serializer(instance)
        return CustomResponse(
            data=serializer.data,
            message="Job Application fetched successfully",
            status_code=status.HTTP_200_OK
        )

    def update(self, request, *args, **kwargs):
        """Update a job application with validation and permission check."""
        try:
            instance = self.get_object()
            self.check_object_permissions(request, instance)
        except PermissionDenied:
            return CustomResponse(
                data={},
                message="You are not authorized to update this job application.",
                status_code=status.HTTP_403_FORBIDDEN
            )
        except NotFound:
            return CustomResponse(
                data={},
                message="Job Application not found.",
                status_code=status.HTTP_404_NOT_FOUND
            )

        # Validate using create serializer
        serializer = self.get_serializer(instance, data=request.data, partial=kwargs.get('partial', False))
        
        if not serializer.is_valid():
            missing_fields = [
                field for field, errors in serializer.errors.items()
                if any(err.code == 'required' for err in errors)
            ]
            if missing_fields:
                return CustomResponse(
                    data=None,
                    message=f"Missing fields: {', '.join(missing_fields)}",
                    status_code=status.HTTP_400_BAD_REQUEST
                )
            return CustomResponse(
                data=None,
                message="Invalid data",
                status_code=status.HTTP_400_BAD_REQUEST
            )

        self.perform_update(serializer)
        
        # Return formatted response using view serializer
        view_serializer = JobApplicationViewSerializer(serializer.instance)
        return CustomResponse(
            data=view_serializer.data,
            message="Job Application updated successfully",
            status_code=status.HTTP_200_OK
        )
    
    def destroy(self, request, *args, **kwargs):
        """Delete a job application with permission check."""
        try:
            instance = self.get_object()
            self.check_object_permissions(request, instance)
        except PermissionDenied:
            return CustomResponse(
                data={},
                message="You are not authorized to delete this job application.",
                status_code=status.HTTP_403_FORBIDDEN
            )
        except NotFound:
            return CustomResponse(
                data={},
                message="Job Application not found.",
                status_code=status.HTTP_404_NOT_FOUND
            )

        self.perform_destroy(instance)
        return CustomResponse(
            data=None,
            message="Job Application deleted successfully",
            status_code=status.HTTP_204_NO_CONTENT
        )

    def list(self, request, *args, **kwargs):
        """
        List job applications for the current user with optional filtering.
        Supports filtering by: employment_type, job_application_status, work_arrangement, company_name, position_title
        """
        # Start with user's job applications
        queryset = JobApplication.objects.filter(user=request.user)
        
        # Extract filter parameters from query string
        employment_type = request.query_params.get('employment_type')
        job_application_status = request.query_params.get('job_application_status')
        work_arrangement = request.query_params.get('work_arrangement')
        company_name = request.query_params.get('company_name')
        position_title = request.query_params.get('position_title')
        
        # Apply filters based on provided parameters
        if employment_type:
            queryset = queryset.filter(employment_type__label__iexact=employment_type)
        
        if job_application_status:
            queryset = queryset.filter(job_application_status__label__iexact=job_application_status)
        
        if work_arrangement:
            queryset = queryset.filter(work_arrangement__label__iexact=work_arrangement)
        
        if company_name:
            # Partial match for company name
            queryset = queryset.filter(company_name__icontains=company_name)
        
        if position_title:
            # Partial match for position title
            queryset = queryset.filter(position_title__icontains=position_title)
        
        # Order by most recent applications first
        queryset = queryset.order_by('-date_applied')
        
        # Handle pagination
        page = self.paginate_queryset(queryset)

        if page is not None:
            serializer = self.get_serializer(page, many=True)
            return CustomResponse(
                data={
                    'results': serializer.data,
                    'current_page': self.paginator.page.number,
                    'total_pages': self.paginator.page.paginator.num_pages,
                    'count': self.paginator.page.paginator.count,
                    'filters_applied': {
                        'employment_type': employment_type,
                        'job_application_status': job_application_status,
                        'work_arrangement': work_arrangement,
                        'company_name': company_name,
                        'position_title': position_title,
                    }
                },
                message="Paginated job applications",
                status_code=status.HTTP_200_OK
            )

        # Return all results if no pagination
        serializer = self.get_serializer(queryset, many=True)
        return CustomResponse(
            data={
                'results': serializer.data,
                'filters_applied': {
                    'employment_type': employment_type,
                    'job_application_status': job_application_status,
                    'work_arrangement': work_arrangement,
                    'company_name': company_name,
                    'position_title': position_title,
                }
            },
            message="All job applications",
            status_code=status.HTTP_200_OK
        )
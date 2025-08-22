from rest_framework import serializers
from .models import JobApplication, EmploymentType, WorkArrangement, JobApplicationStatus, Reminder

class JobApplicationCreateSerializer(serializers.ModelSerializer):
    class Meta:
        model = JobApplication
        fields = [
            'position_title',
            'company_name',
            'employment_type',
            'work_arrangement',
            'job_application_status',
            'job_posting_link',
            'date_applied',
            'job_location',
            'job_description',
        ]

    def validate(self, attrs):
        # Only validate required fields for CREATE operations or full UPDATE (PUT)
        # For partial updates (PATCH), skip this validation
        if not self.partial:
            required_fields = self.Meta.fields
            missing = [field for field in required_fields if attrs.get(field) in [None, '']]

            if missing:
                field_list = ', '.join(missing)
                raise serializers.ValidationError(f"Missing required fields: {field_list}")

        return attrs

class EmploymentTypeSerializer(serializers.ModelSerializer):
    class Meta:
        model = EmploymentType
        fields = ['id','label']

class WorkArrangementSerializer(serializers.ModelSerializer):
    class Meta:
        model = WorkArrangement
        fields = ['id','label']

class JobApplicationStatusSerializer(serializers.ModelSerializer):
    class Meta:
        model = JobApplicationStatus
        fields = ['id','label']

class JobApplicationViewSerializer(serializers.ModelSerializer):
    employment_type = EmploymentTypeSerializer(read_only=True)
    work_arrangement = WorkArrangementSerializer(read_only=True)
    job_application_status = JobApplicationStatusSerializer(read_only=True)

    class Meta:
        model = JobApplication
        fields = [
            'id',
            'position_title',
            'company_name',
            'employment_type',
            'work_arrangement',
            'job_application_status',
            'job_posting_link',
            'date_applied',
            'job_location',
            'job_description',
            'created_at',
            'updated_at',
        ]

class ReminderSerializer(serializers.ModelSerializer):
    class Meta:
        model = Reminder
        fields = '__all__'
        read_only_fields = ['job_application', 'created_at', 'modified_at']
        
    def validate(self, attrs):
        # Only validate required fields for CREATE operations or full UPDATE (PUT)
        # For partial updates (PATCH), skip this validation
        if not self.partial:
            # Get all field names defined on the serializer (excluding read-only ones)
            required_fields = [
                field_name for field_name, field in self.fields.items()
                if not field.read_only and field.required
            ]
            
            # Check which ones are missing or empty
            missing = [field for field in required_fields if attrs.get(field) in [None, '']]

            if missing:
                field_list = ', '.join(missing)
                raise serializers.ValidationError(f"Missing required fields: {field_list}")

        return attrs
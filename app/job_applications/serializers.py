from rest_framework import serializers
from .models import JobApplication, EmploymentType, WorkArrangement, JobApplicationStatus

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
            'company_logo_url',
            'job_location',
            'job_description',
        ]

    def validate(self, attrs):
        required_fields = self.Meta.fields
        missing = [field for field in required_fields if attrs.get(field) in [None, '']]

        if missing:
            field_list = ', '.join(missing)
            raise serializers.ValidationError(f"Missing required fields: {field_list}")

        return attrs

class EmploymentTypeSerializer(serializers.ModelSerializer):
    class Meta:
        model = EmploymentType
        fields = ['label']

class WorkArrangementSerializer(serializers.ModelSerializer):
    class Meta:
        model = WorkArrangement
        fields = ['label']

class JobApplicationStatusSerializer(serializers.ModelSerializer):
    class Meta:
        model = JobApplicationStatus
        fields = ['label']

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
            'company_logo_url',
            'job_location',
            'job_description',
        ]

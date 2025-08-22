from django.db import models
from users.models import User
from django.utils import timezone

# Create your models here.

class EmploymentType(models.Model):
    id = models.AutoField(primary_key=True)
    label = models.CharField(max_length=128)
    description = models.CharField(max_length=128)
    
    def __str__(self):
        return self.label

    class Meta:
        db_table = 'tbl_employment_type'

class WorkArrangement(models.Model):
    id = models.AutoField(primary_key=True)
    label = models.CharField(max_length=128)
    description = models.CharField(max_length=128)
    
    def __str__(self):
        return self.label

    class Meta:
        db_table = 'tbl_work_arrangement'

    
class JobApplicationStatus(models.Model):
    id = models.AutoField(primary_key=True)
    label = models.CharField(max_length=128)
    description = models.CharField(max_length=128)
    
    def __str__(self):
        return self.label

    class Meta:
        db_table = 'tbl_job_application_status'

class JobApplication(models.Model):
    id = models.AutoField(primary_key=True)
    user = models.ForeignKey(User, on_delete=models.CASCADE, related_name='job_application')
    position_title = models.CharField(max_length=128)
    company_name = models.CharField(max_length=128)
    employment_type = models.ForeignKey(EmploymentType, on_delete=models.SET_NULL, null=True, related_name='employment_type')
    work_arrangement = models.ForeignKey(WorkArrangement, on_delete=models.SET_NULL, null=True, related_name='work_arrangement')
    job_application_status = models.ForeignKey(JobApplicationStatus, on_delete=models.SET_NULL, null=True, related_name='job_application_status')
    job_posting_link = models.CharField(max_length=255)
    date_applied = models.DateField()
    job_location = models.CharField(max_length=255)
    job_description = models.TextField(null=True, blank=True)
    created_at = models.DateTimeField(auto_now_add=True)
    updated_at = models.DateTimeField(auto_now=True)
    
    def __str__(self):
        return f"{self.position_title} at {self.company_name}"

    class Meta:
        ordering = ['-created_at']
        db_table = 'tbl_job_application'


class Reminder(models.Model):
    id = models.AutoField(primary_key=True)
    job_application = models.ForeignKey(JobApplication, on_delete=models.CASCADE, related_name='reminders')
    title = models.CharField(max_length=255)
    description = models.TextField(blank=True, null=True)
    is_enabled = models.BooleanField(default=True)
    reminder_datetime = models.DateTimeField(default=timezone.now)
    created_at = models.DateTimeField(auto_now_add=True)
    modified_at = models.DateTimeField(auto_now=True)
    
    def __str__(self):
        return f"Reminder: {self.title}"

    class Meta:
        db_table = 'tbl_reminder'
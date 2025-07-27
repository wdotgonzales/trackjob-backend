from django.db import models
from django.utils import timezone

class User(models.Model):
    id = models.AutoField(primary_key=True)
    username = models.CharField(max_length=150, unique=True)
    email = models.EmailField(unique=True)
    password = models.CharField(max_length=128)
    date_joined = models.DateTimeField(default=timezone.now)

    def __str__(self):
        return self.username

    class Meta:
        db_table = 'tbl_user' 
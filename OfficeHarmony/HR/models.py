from django.db import models
from django.contrib.auth.models import User


class Emp(models.Model):
    user = models.ForeignKey('auth.User', on_delete=models.DO_NOTHING)
    fname = models.CharField(max_length=64)
    lname = models.CharField(max_length=64)
    email = models.CharField(max_length=64, default='ex@g.c')
    birthdate = models.DateField()
    startdate = models.DateField()
    department = models.CharField(max_length=64)
    role = models.CharField(max_length=64)
    education = models.TextField()
    permit = models.IntegerField()
    on_leave = models.IntegerField(default=0)
    leave_type = models.CharField(max_length=64, null=True)

class Payroll(models.Model):
    emp_id = models.ForeignKey('Emp', on_delete=models.CASCADE)
    base_salary = models.FloatField()
    #to be manuplated in views.py
    benefit = models.CharField(null=True, max_length=255)
    retirement = models.IntegerField(null=True)
    #bonus - percentage
    bonus = models.FloatField(null=True)


class Attend(models.Model):
    date = models.DateField()
    present = models.ManyToManyField('Emp', related_name="presentee")
    absent = models.ManyToManyField('Emp', related_name="absentee")
    open = models.IntegerField(default=1)

class Request(models.Model):
    time = models.DateTimeField(auto_now_add=True)
    req_type =  models.CharField(max_length=64)
    req_name = models.CharField(max_length=255, null=True)
    emp_id = models.ForeignKey('Emp', on_delete=models.CASCADE)
    description = models.CharField(max_length=255, null=True)
    link = models.TextField(null=True)
    answer = models.TextField(null=True)
    open = models.IntegerField(default=1)

class Job_Post(models.Model):
    position = models.CharField(max_length=255)
    department = models.CharField(max_length=255)
    no_applicants = models.IntegerField()
    no_positions = models.IntegerField()
    description = models.TextField()
    startdate = models.DateField()
    enddate = models.DateField()
    review_startdate = models.DateField()
    onboarding_date = models.DateField()

class Applicant(models.Model):
    post = models.ForeignKey("Job_Post", on_delete=models.CASCADE)
    fname = models.CharField(max_length=64)
    lname = models.CharField(max_length=64)
    email = models.CharField(max_length=64, default='ex@g.c')
    education = models.TextField(null=True)
    file = models.TextField(null=True)
    interview = models.TextField(null=True)

class Training(models.Model):
    name = models.CharField(max_length=64)
    lecture = models.TextField(null=True)
    assess = models.TextField(null=True)

class Track_Training(models.Model):
    training = models.ForeignKey("Training", on_delete=models.CASCADE)
    emp_id = models.ForeignKey('Emp', on_delete=models.CASCADE)
    assess = models.TextField(null=True)
    progress = models.IntegerField(default=0)

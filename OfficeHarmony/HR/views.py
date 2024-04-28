from django.shortcuts import render
from django.urls import reverse
from django.http import HttpResponse, HttpResponseRedirect
from .models import Emp, Job_Post, Applicant, Payroll, Training, Track_Training, Attend, Request
from django.contrib.auth.models import User
from django.db import IntegrityError
from django.views.decorators.csrf import csrf_exempt
import random
from django.contrib.auth import authenticate, login, logout
from django.core.paginator import Paginator
import datetime
from django.contrib import messages
import os
from django.http import FileResponse
from django.shortcuts import get_object_or_404
from django.conf import settings
import json
import math
from django.http import JsonResponse

@csrf_exempt
def login_view(request):
    if request.method == "POST":
        if not request.POST.get("email"):
            return render(request, 'login.html', {
                "error":"Must Provide Email."
            })
        elif not request.POST.get("psw"):
            return render(request, 'login.html', {
                "error":"Must Provide Passowrd."
            })
        email = request.POST.get("email")
        user = authenticate(request, username=email, password=request.POST["psw"])

        if user is not None:
            login(request, user)
            emp = Emp.objects.filter(user=request.user).first()
            if emp.permit == 1: 
                return HttpResponseRedirect(reverse("emp_data"))
            elif emp.permit == 2: 
                return HttpResponseRedirect(reverse("recruit"))
            elif emp.permit == 3:
                return HttpResponseRedirect(reverse("payroll"))
            elif emp.permit == 4:
                return HttpResponseRedirect(reverse("training"))
            elif emp.permit == 0:
                print("11")
                return HttpResponseRedirect(reverse("emp"))
        else:
            print("ya")
            return render(request, 'login.html', {
                "error":"Invalid Credentials."
            })
    return render(request, 'login.html')

def logout_view(request):
    logout(request)
    return HttpResponseRedirect(reverse("login"))


@csrf_exempt
def emp_data(request):
    emp = Emp.objects.filter(user=request.user).first()
    if emp.permit != 1:
        return render(request, 'login.html', {
                "error":"Not Allowed"
            })
    if request.method == "POST":
        if "add_employee" in request.POST:
            psw = str(random.randint(1000000, 9999999))
            print(psw)
            try:
                user = User.objects.create_user(request.POST["email"], request.POST["email"],psw)
                new_user = User.objects.get(email=request.POST["email"])
                emp = Emp(user=new_user,fname=request.POST["fname"], lname=request.POST["lname"], email= request.POST["email"], birthdate=request.POST["birthdate"], startdate=request.POST["startdate"], department=request.POST["dep"], role=request.POST["role"], education=request.POST["edu"], permit=int(request.POST["permit"]))
                user.save()
                emp.save()
                if request.POST.get('tr'):
                    print("dd")
                    check_tr = Training.objects.filter(pk=request.POST.get('tr')).first()
                    if check_tr is not None:
                        print("DD")
                        new_tr = Track_Training(training=check_tr, emp_id=emp)
                        new_tr.save()
                return HttpResponseRedirect(reverse("emp_data"))
            except IntegrityError:
                messages.error(request, 'Email address already taken.')
                return HttpResponseRedirect(reverse("emp_data"))
        elif "fire_employee" in request.POST:
            emp_email = request.POST["email"]
            hr_psw = request.POST["psw"]

            validate = authenticate(request, username=request.user.username, password=request.POST["psw"])

            if validate is  None:
                return render(request, 'empdb.html', {
                    "error": "Incorrect staff password"
                })
            
            User.objects.get(username=emp_email).delete()
        elif "accept" in request.POST:
            req_id = request.POST["req_id"]
            req = Request.objects.get(pk=req_id)
            req.answer = 'approved'
            req.open = 0
            req_emp = Emp.objects.get(pk=req.emp_id.id)
            req_emp.on_leave = 1
            req_emp.leave_type = req.req_name
            req_emp.save()
            req.save()

        elif "reject" in request.POST:
            req_id = request.POST["req_id"]
            req = Request.objects.get(pk=req_id)
            req.answer = 'rejected'
            req.open = 0
            req.save()
        elif "remove_leave" in request.POST:
            req_emp = Emp.objects.get(pk=request.POST["emp_id"])
            req_emp.on_leave = 0
            req_emp.leave_type = None
            req_emp.save()
        return HttpResponseRedirect(reverse("emp_data"))
    else:
        result = 0
        if 'report' in request.GET:
            startdate = request.GET['startdate']
            enddate = request.GET['enddate']
            result = Attend.objects.filter(date__range=(startdate,enddate))
        emp_data = Emp.objects.all().order_by("startdate")
        paginator = Paginator(emp_data, 10)

        if request.GET.get('page') == None:
            page_number = 1
        else:
            page_number = request.GET.get('page')
        if request.GET.get('next'):
            page_number = int(page_number) + 1
        elif request.GET.get('previous'):
            page_number = int(page_number) - 1
        page_obj = paginator.get_page(page_number)
        training = Training.objects.all()
        date=datetime.date.today()
        check = Attend.objects.filter(date=date)
        if check.count()==0:
            new = Attend(date=date)
            new.save()
        rolls = Attend.objects.filter(open=1)
        req = Request.objects.filter(open=1).filter(req_type="leave")
        return render(request, 'empdb.html', {
            "emp_data":page_obj,
            "has_pages":(paginator.num_pages>1),
            "page_number":page_number,
            "has_next":page_obj.has_next(),
            "has_previous":page_obj.has_previous(),
            "training":training,
            "rolls":rolls,
            "report":result,
            "leave_requests":req
        })

def recruit(request):
    emp = Emp.objects.filter(user=request.user).first()
    if emp.permit != 2 and emp.permit != 1 :
        return render(request, 'login.html', {
                "error":"Not Allowed"
        })
    
    if request.method == "POST":
        if 'job_post' in request.POST:
            today = datetime.date.today()
            post = Job_Post(position=request.POST["position"], department=request.POST["dep"], no_applicants=request.POST["no_applicant"], no_positions = request.POST["no_position"], description=request.POST["description"], startdate = request.POST["startdate"], enddate = request.POST["enddate"], review_startdate = request.POST["review_startdate"], onboarding_date = request.POST["onboarding_date"])
            post.save()
            return HttpResponseRedirect(reverse("recruit"))
    else:
        posts = Job_Post.objects.all().order_by("-review_startdate")
        return render(request, 'recruit.html', {
            "posts":posts
        })
    
@csrf_exempt
def job_post(request, post_id):
    emp = Emp.objects.filter(user=request.user).first()
    if emp.permit != 2 and emp.permit != 1 :
        return render(request, 'login.html', {
                "error":"Not Allowed"
        })
    post = Job_Post.objects.get(id=post_id)
    apps = Applicant.objects.filter(post=post)
    if request.method == "POST":
        if 'log' in request.POST and request.FILES.get('file'):
            apps = Applicant.objects.filter(post=post)
            if apps.count() == int(post.no_applicants):
                return render(request, 'job_post.html', {
                        "post":post,
                        "apps":apps,
                        "message":"Maximum Number reached."
                    })
            for app in apps:
                if app.email == request.POST['email']:
                    return render(request, 'job_post.html', {
                        "post":post,
                        "apps":apps,
                        "message":"Applicant already exists in the current post"
                    })
            file = request.FILES['file']
            log = Applicant(post=post, fname=request.POST['fname'], lname=request.POST['lname'], email=request.POST['email'], education=request.POST['edu'], file=file.name)
            log.save()
            
            file = request.FILES['file']
            log_id = log.id
            directory = f'recruit/{post_id}/'
            os.makedirs(directory, exist_ok=True)
            filename = f'{log_id}_{file.name}'
            file_path = os.path.join(directory, filename)
            with open(file_path, 'wb+') as destination:
                for chunk in file.chunks():
                    destination.write(chunk)
        else:
            data = json.loads(request.body)
            app_id = data.get("app_id", "")
            content = data.get("content", "")
            app = Applicant.objects.get(pk=app_id)
            app.interview = content
            app.save()
            app = Applicant.objects.get(pk=app_id)
    if post is not None:
        return render(request, 'job_post.html', {
            "post":post,
            "apps":apps
        })
    else:
        return HttpResponseRedirect(reverse("recruit"))

def download(request, post_id, app_id):
    applicant = get_object_or_404(Applicant, pk=app_id, post_id=post_id)
    directory = os.path.join(settings.MEDIA_ROOT, f'recruit/{post_id}/')
    filename = f'{applicant.id}_{applicant.file}'
    file_path = os.path.join(directory, filename)
    if os.path.exists(file_path):
        with open(file_path, 'rb') as file:
            file_content = file.read()
            response = HttpResponse(file_content, content_type='application/octet-stream')
            response['Content-Disposition'] = f'attachment; filename="{filename}"'
            return response
    else:
        return HttpResponse("File not found", status=404)

@csrf_exempt
def payroll(request):
    emp_check = Emp.objects.filter(user=request.user).first()
    if emp_check.permit != 3 and emp_check.permit != 1 :
        return render(request, 'login.html', {
                "error":"Not Allowed"
        })
    if request.method == "POST":
        if 'add_payroll' in request.POST:
            emp_id = request.POST['emp_id']
            emp_check = Emp.objects.filter(id=emp_id).first()
            pay_check = Payroll.objects.filter(emp_id=emp_check)
            if pay_check.count() != 0:
                print("1")
                return HttpResponseRedirect(reverse("payroll"))
            if float(request.POST.get('bonus')) >= 1:
                print("2")    
                return HttpResponseRedirect(reverse("payroll"))
            print(request.POST.get('my_retirement'))
            payroll = Payroll(emp_id=emp_check, base_salary=request.POST['base'], benefit=request.POST.getlist('benefit'), retirement=request.POST.get('retirement'), bonus=request.POST.get('bonus'))
            payroll.save()
    
    emps = Emp.objects.all()
    list = []
    for emp in emps:
        payroll = Payroll.objects.filter(emp_id=emp)
        if payroll.count() == 0:
            list.append(emp)
    return render(request, 'payroll.html', {
            "emps":list
        })

@csrf_exempt
def training(request):
    emp = Emp.objects.filter(user=request.user).first()
    if emp.permit != 4 and emp.permit != 1 :
        return render(request, 'login.html', {
                "error":"Not Allowed"
        })
    if request.method == "POST":
        if 'add_training' in request.POST:
            new = Training(name=request.POST['name'], lecture=request.POST['lecture'], assess=request.POST['assess'])
            new.save()
        elif 'accept' in request.POST:
            entity = Track_Training.objects.get(pk=int(request.POST['id']))
            entity.progress = 3
            entity.save()
        elif 'reject' in request.POST:
            entity = Track_Training.objects.get(pk=request.POST['id'])
            entity.progress = 2
            entity.save()
    training = Training.objects.all()
    sub = Track_Training.objects.filter(progress=1)
    accept = Track_Training.objects.filter(progress=3)
    return render(request, 'training.html', {
        "training": training,
        "sub":sub,
        "accept":accept
    })

@csrf_exempt
def emp(request):
    emp = Emp.objects.filter(user=request.user).first()
    if request.method == "POST":
        if "request" in request.POST:
            new_req = Request(req_type='leave', emp_id=emp, req_name=request.POST.get('leave_type'),description=request.POST.get('description'), link=request.POST.get('link'))
            new_req.save()
        else:
            data = json.loads(request.body)
            track_id = data.get("track_id", "")
            content = data.get("content", "")
            progress = data.get("progress", "")
            track = Track_Training.objects.get(pk=track_id)
            track.assess = content
            track.progress =progress
            track.save()
    payroll = Payroll.objects.filter(emp_id=emp).last()
    training_due = Track_Training.objects.filter(emp_id=emp).filter(progress=0)
    sub = Track_Training.objects.filter(emp_id=emp).filter(progress=1)
    reject = Track_Training.objects.filter(emp_id=emp).filter(progress=2)
    accept = Track_Training.objects.filter(emp_id=emp).filter(progress=3)
    date = datetime.date.today()
    attend = Attend.objects.filter(date=date).last()
    
    attended = False
    absented = False
    if attend is not None:
        if emp in attend.present.all():
            attended = True
        if emp in attend.absent.all():
            absented = True

    absentee = emp.absentee.all().count()
    return render(request, 'emp.html', {
        "emp": emp,
        "payroll": payroll,
        "training_due":training_due,
        "sub":sub,
        "reject":reject,
        "accept":accept,
        "datetime":datetime.date.today(),
        "attended":attended,
        "absented":absented,
        "absentee":absentee
    })

@csrf_exempt
def attend(request):
    emp = Emp.objects.filter(user=request.user).first()
    if request.method == "POST":
        if "close_roll" in request.POST:
            attend = Attend.objects.get(pk=request.POST['attend_id'])
            attend.open = 0
            all_emp = Emp.objects.filter(on_leave=0)
            for entity in all_emp:
                if entity not in attend.present.all():
                    attend.absent.add(entity)
            attend.save()
            return HttpResponseRedirect(reverse("emp_data"))
        else:
            data = json.loads(request.body)
            user_id = data.get("user_id", "")
            date = datetime.date.today()
            lat = float(data.get("lat", ""))
            lon = float(data.get("lon", ""))
            traget_lat = 25.4123
            target_lon = 55.5066
            attend = Attend.objects.filter(date=date).first()
            if attend is None:
                attend = Attend(date=date)
                attend.save()
            
            maximum = 200
            print(attend)
            distance = haversine(lat,lon,traget_lat,target_lon)
            print(distance)
            if distance <= maximum:
                attend.present.add(emp)
                attend.save()
                return JsonResponse({"message": "Success"}, status=201)
            else:
                return JsonResponse({"message": "Invalid Location."}, status=400)
    return HttpResponseRedirect(reverse("emp"))

def haversine(lat1, lon1, lat2, lon2):
    R = 6371.0
    lat1_rad = math.radians(lat1)
    lon1_rad = math.radians(lon1)
    lat2_rad = math.radians(lat2)
    lon2_rad = math.radians(lon2)
    dlat = lat2_rad - lat1_rad
    dlon = lon2_rad - lon1_rad
    a = math.sin(dlat / 2)**2 + math.cos(lat1_rad) * math.cos(lat2_rad) * math.sin(dlon / 2)**2
    c = 2 * math.atan2(math.sqrt(a), math.sqrt(1 - a))
    distance = R * c

    return distance
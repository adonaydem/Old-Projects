from django.urls import path
from . import views

urlpatterns = [
    path('emp_data', views.emp_data, name='emp_data'),
    path('', views.login_view, name='login'),
    path('logout', views.logout_view, name='logout'),
    path('recruit', views.recruit, name='recruit'),
    path('payroll', views.payroll, name='payroll'),
    path('training', views.training, name='training'),
    path('emp', views.emp, name='emp'),
    path('attend', views.attend, name='attend'),
    path('job_post/<int:post_id>/', views.job_post, name='job_post'),
    path('download/<int:post_id>/<int:app_id>/', views.download, name='download')
]
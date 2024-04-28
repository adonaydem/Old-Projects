# CS50W Final Project
## Office Harmony - Human Resources Information System
#### Description:

** Use the following credentials to get started: *
**Username: adu@g.c
password: 7773788**


I am Adonay Demewez Gebremedhin and this is my CS50W final project. In this project, I have tried to draw upon the experiences I have gained from the weeks of CS50w lectures and problems. I, specifically, used Python and JavaScript to implement a dynamic web application.

This web application contains various platforms that will enable a certain company to automate its HR processes. It provides five basic uses: General Data Processing, Recruitment System, Attendance Tracking System, Payroll Management and Employee Training and Career Development.

## Distinctiveness and Complexity
Unlike the projects that I was instructed to implement under this course, this project contains various distinguishing features that will make it stand out. 

### Attendance Tracking
This feature uses location tracking of employees by utilizing geolocation feature of Javascript. It the uses a mathematical theory called Haversine function to calculate the distance between two locations on the earth's surface.

### File handling
The recruitment system uses file handling mechanisms of python, to handle applicant's resumes(CV). It enables HR personell to upload multiple file types. Consequently, interviewers can download and evaluate these files.

### Project Walkthrough
The project uses ordinary Django file structure. There is an app called HR in the project's main directory that contains all the features.

There is a directory called recruit to handle files for the recruitment system.

In the 'HR' directory, you will find the templates and the views required to run this project.

There are no external packages that you need to install, in order to run the application.

### How to run the application
You can run this application by simply writing **'python manage.py runserver'.**
Before that, it's recommended to run migrations:
**'python manage.py makemigrations'
'python manage.py migrate'**

**Use the following credentials to get started: **

**Username: adu@g.c
password: 7773788**

## Features

### Employee Data Management
This feature enables the HR department to manuplate employee data. The staff has access to a database that contains user personal and instituional details. They also have the enability manage the Attendance of the entire company. They can close attendance rolls of the day and they can recieve and process leave permission requests. In addition, they can generate reports of attendance information of users within specfifc dates. They also have access to add and remove employees from the database.

### Recruitment System
This is another integral part of the HR department that enables the staff to recruit employees. They can keep track of Job posts that they have created. They can also store applicant data based on that certain post. In addition they can log interview history, for latter decisions. They have the ability to store applicant CV for latter evaluations.

### Initial Payroll Management
Here, you have the ability to Add initail payroll info of recently joined employees. The staff can decide on base salary, Benefits, Retirement plans and Bonus. This feature has been minimized so that it can be transferred to the Finance department.

### Training and Development
Here, the staff has the capability to add lecture contents using links of various resources and Assessment requirements needed to complete that training. The staff can track submissions and accept and reject them.

### Employee Self Service
Employees have the capability to use the above resources of the HR department using their own platform. 
#### Attendance
They can mark their attendance assuming they satisfy the location requirement of the system. They can see their attendace records, esp. their 'Absent Records'. In addition, they can request for leave permission, that will be submited to the HR staff. Once their request is accepted, they are exempted from being marked as absent.

#### Personal Details
Here, the employee can see his names, email, important dates and the department he is currently working for.

#### Payroll
Here, the employee can see what his salary info is, as well as his benefits.

#### Training
Here, the employee can attend whatever training he has been assigned to accomplish. He can submit assessments and get feedback.


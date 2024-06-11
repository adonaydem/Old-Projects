import os
import random
import pdfkit
from cs50 import SQL
from flask import (
    Flask,
    flash,
    redirect,
    render_template,
    render_template_string,
    request,
    session,
)
from flask_session import Session
from werkzeug.security import check_password_hash, generate_password_hash
from functools import wraps
from datetime import date, datetime
from flask_mail import Mail, Message


app = Flask(__name__)

app.config['MAIL_SERVER']='smtp.sendgrid.net'
app.config['MAIL_PORT'] = 465
app.config['MAIL_USERNAME'] = 'apikey'
app.config['MAIL_PASSWORD'] = input()
app.config['MAIL_USE_TLS'] = False
app.config['MAIL_USE_SSL'] = True

mail = Mail(app)


app.config["SESSION_PERMANENT"] = False
app.config["SESSION_TYPE"] = "filesystem"
Session(app)

db = SQL("sqlite:///pharmacy.db")
row4 = db.execute("select * from users where type='admin'")
if len(row4) == 0:
    db.execute("insert into users (name,birthdate,type,password,email,startdate) values(?,?,?,?,?,?)", 'admin', '2000-10-10', 'admin', generate_password_hash(str(12345)), 'admin@natna.ph','2020-01-01')
    rows = db.execute("select * from users where email=?", 'admin@natna.ph')
    db.execute("insert into salary (user_id,amount,type,startdate) values(?,?,?,?)", rows[0]['id'], 10000, 1,'2020-01-01')
def login_required(f):
    @wraps(f)
    def decorated_function(*args, **kwargs):
        if session.get("user_id") is None:
            return redirect("/")
        return f(*args, **kwargs)
    return decorated_function

@app.route("/", methods=["GET", "POST"])
def login():
    session.clear()
    row4 = db.execute("select * from users where type='admin'")

    if len(row4) == 0:
        return render_template("login.html", error='admin_required')
    if request.method == "POST":
        if not request.form.get("email"):
            return render_template("login.html", error='email')
        elif not request.form.get("password"):
            return render_template("login.html", error='psw')

        rows = db.execute("select * from users where email=?", request.form.get("email"))
        if len(rows) != 1 or  not check_password_hash(rows[0]["password"], request.form.get("password")):
            return render_template("login.html", error='invalid')

        session["user_id"] = rows[0]["id"]
        if rows[0]['type'] == 'hr_manager':
            return redirect("/hr_manager")
        elif rows[0]['type'] == 'acc_manager':
            return redirect("/acc_manager")
        elif rows[0]['type'] == 'admin':
            return redirect("/admin")
        elif rows[0]['type'] == 'sales':
            return redirect("/sales")
        elif rows[0]['type'] == 'logistics':
            return redirect("/logistics")
        elif rows[0]['type'] == 'pharmacy':
            return redirect("/sales")
        else:
            return render_template("login.html", error='type')
    else:
        print(request.args.get('error'))
        return render_template("login.html", error=request.args.get('error'))

@app.route("/logout")
def logout():
    session.clear()

    return redirect("/")

@app.route("/hr_manager", methods=["GET", "POST"])
@login_required
def hr_manager():
    row = db.execute("select * from users where id=?",session["user_id"])
    if row[0]['type'] != 'hr_manager' and row[0]['type'] != 'admin':
        return redirect("/")
    if row[0]['type'] == 'admin':
        session['admin'] = 2
    else:
        session['admin'] = 0
    if request.method == "POST":
        if "add_employee" in request.form:
            if not request.form.get("email"):
                return render_template("hr_manager.html", error='email')
            elif not request.form.get("birthdate"):
                return render_template("hr_manager.html", error='hbd')
            elif not request.form.get("name"):
                return render_template("hr_manager.html", error='name')
            elif not request.form.get("type"):
                return render_template("hr_manager.html", error='type')
            elif not request.form.get("startdate"):
                return render_template("hr_manager.html", error='startdate')
            elif not request.form.get("salary"):
                return render_template("hr_manager.html", error='salary')

            rows = db.execute("select * from users where email=?", request.form.get("email"))

            if len(rows) != 0:
                return render_template("hr_manager.html", error='emaildp')

            if request.form.get("type") == 'hr_manager' or request.form.get("type") == 'acc_manager':
                row2 = db.execute("select * from users")
                for row in row2:
                    if row['type'] == request.form.get("type"):
                        return render_template("hr_manager.html", error='manager_already_exists')


            psw = random.randint(1000000, 9999999)
            msg = Message('Account Information for Natna Pharmacy Management System', sender = 'demewezseyoum@gmail.com', recipients = [request.form.get("email")])
            msg.body = "Dear Employee,\n\n Congratulations!!!!. Your account has been created succefully.\n You can use your email address and the following password to access the system: " + str(psw)
            mail.send(msg)
            print(psw)
            psw = generate_password_hash(str(psw))

            db.execute("insert into users (name,birthdate,type,password,email,startdate) values(?,?,?,?,?,?)", request.form.get("name"), request.form.get("birthdate"), request.form.get("type"), psw, request.form.get("email"),request.form.get("startdate"))
            rows = db.execute("select * from users where email=?", request.form.get("email"))
            db.execute("insert into salary (user_id,amount,type,startdate) values(?,?,?,?)", rows[0]['id'], request.form.get("salary"), 1,request.form.get("startdate"))

            return redirect("/hr_manager")
        elif "fire_employee" in request.form:
            if not request.form.get("email"):
                return render_template("hr_manager.html", error='email')
            elif not request.form.get("password"):
                return render_template("hr_manager.html", error='psw')

            rows = db.execute("select * from users where email=?", request.form.get("email"))
            if session['admin'] == 0:
                if rows[0]['type'] == 'admin' or rows[0]['type'] == 'hr_manager' or rows[0]['type'] == 'acc_manager':
                    return render_template("hr_manager.html", error='unauthorized_access')

            if rows[0]['id'] == session['user_id']:
                return render_template("hr_manager.html", error='email404')
            if len(rows) != 1:
                return render_template("hr_manager.html", error='email404')
            if not check_password_hash(row[0]["password"], request.form.get("password")):
                return render_template("hr_manager.html", error='psw')
            db.execute("delete from salary where user_id=?", rows[0]['id'])
            db.execute("delete from users where email=?", request.form.get("email"))
            return redirect("/hr_manager")
        elif "add_supplier" in request.form:
            if not request.form.get("email"):
                return render_template("hr_manager.html", error='email')
            elif not request.form.get("company_name"):
                return render_template("hr_manager.html", error='name')
            elif not request.form.get("contact_name"):
                return render_template("hr_manager.html", error='name')
            elif not request.form.get("startdate"):
                return render_template("hr_manager.html", error='startdate')
            elif not request.form.get("address"):
                return render_template("hr_manager.html", error='address')

            rows = db.execute("select * from supplier where email=?", request.form.get("email"))

            if len(rows) != 0:
                return render_template("hr_manager.html", error='email_duplicate')

            db.execute("insert into supplier (company_nam,address,email,startdate,sales,contact_name) values(?,?,?,?,?,?)", request.form.get("company_name"), request.form.get("address") , request.form.get("email"),request.form.get("startdate"),0,request.form.get("contact_name"))
            return redirect("/hr_manager")
        elif "update_salary" in request.form:
            if not request.form.get("email"):
                return render_template("hr_manager.html", error='email')
            elif not request.form.get("password"):
                return render_template("hr_manager.html", error='psw')
            elif not request.form.get("salary"):
                return render_template("hr_manager.html", error='salary')
            rows = db.execute("select * from users where email=?", request.form.get("email"))

            if rows[0]['id'] == session['user_id']:
                return render_template("hr_manager.html", error='email404')
            if len(rows) != 1:
                return render_template("hr_manager.html", error='email404')

            if not check_password_hash(row[0]["password"], request.form.get("password")):
                return render_template("hr_manager.html", error='psw')

            row2 = db.execute("select * from salary where user_id=?", rows[0]['id'])

            if len(row2) != 1:
                return render_template("hr_manager.html", error='salary404')

            db.execute("update salary set amount=? where user_id=?", request.form.get("salary"), rows[0]['id'])

            return redirect("/hr_manager")
    else:
        if not request.args.get("more") and not request.args.get("less"):
            session['count'] = 0
        elif request.args.get("more"):
            session['count'] = session['count'] + 5
        elif request.args.get("less"):
            if  session['count'] >= 0 and session['count'] <= 5:
                session['count'] = 0
            else:
                session['count'] = session['count'] - 5
        rows = db.execute("select * from users order by id limit 5 offset ?", session['count'])
        if len(rows) != 0:
            users = []
            for entity in rows:
                user = {}
                user['id'] = entity['id']
                user['name'] = entity['name']
                user['type'] = entity['type']
                user['email'] = entity['email']
                user['birthdate'] = entity['birthdate']
                user['startdate'] = entity['startdate']
                row2 = db.execute("select * from salary where user_id=?", entity['id'])
                user['salary'] = row2[0]['amount']
                users.append(user)
            return render_template("hr_manager.html", users=users)
        else:
            return render_template("hr_manager.html", error=request.args.get('error'), users='None')



@app.route("/acc_manager", methods=["GET", "POST"])
@login_required
def acc_manager():
    print("dd")
    row = db.execute("select * from users where id=?",session["user_id"])
    if row[0]['type'] != 'acc_manager' and row[0]['type'] != 'admin':
        return redirect("/")
    if row[0]['type'] == 'admin':
        session['admin'] = 1
    if request.method == "POST":
         if "account" in request.form:
            if not request.form.get("amount"):
                return render_template("acc_manager.html", error='amount')
            elif not request.form.get("type"):
                return render_template("acc_manager.html", error='type')
            elif not request.form.get("password"):
                return render_template("acc_manager.html", error='password')

            row = db.execute("select * from users where id=?",session["user_id"])
            if not check_password_hash(row[0]["password"], request.form.get("password")):
                return render_template("acc_manager.html", error='psw')

            rows = db.execute("select * from account order by time desc limit 1")

            if len(rows) == 0:
                db.execute("insert into account(type,reason,amount,net) values(?,?,?,?)", request.form.get("type"), request.form.get("reason"), request.form.get("amount"), request.form.get("amount"))
            else:
                if request.form.get("type")=="Deposit":
                    db.execute("insert into account(type,reason,amount,net) values(?,?,?,?)", request.form.get("type"), request.form.get("reason"), request.form.get("amount"), int(rows[0]['net']) + int(request.form.get("amount")))
                elif request.form.get("type")=="Withdraw":
                    net = int(rows[0]['net']) - int(request.form.get("amount"))
                    if net >= 0:
                        db.execute("insert into account(type,reason,amount,net) values(?,?,?,?)", request.form.get("type"), request.form.get("reason"), request.form.get("amount"), net)
                    else:
                        return render_template("acc_manager.html", error='limit')
            return redirect("/acc_manager")
    else:
        if not request.args.get("more") and not request.args.get("less"):
            session['count'] = 0
        elif request.args.get("more"):
            session['count'] = session['count'] + 5
        elif request.args.get("less"):
            if  session['count'] >= 0 and session['count'] <= 5:
                session['count'] = 0
            else:
                session['count'] = session['count'] - 5
        rows = db.execute("select * from account order by time desc limit 5  offset ?", session['count'])
        if len(rows) != 0:
            accounts = []
            for entity in rows:
                account = {}
                account['id'] = entity['id']
                account['time'] = entity['time']
                account['type'] = entity['type']
                account['amount'] = entity['amount']
                account['net'] = entity['net']
                account['reason'] = entity['reason']
                accounts.append(account)
        else:
            accounts = 'None'


        if not request.args.get("more2") and not request.args.get("less2"):
            session['count2'] = 0
        elif request.args.get("more2"):
            session['count2'] = session['count2'] + 5
        elif request.args.get("less2"):
            if  session['count2'] >= 0 and session['count2'] <= 5:
                session['count2'] = 0
            else:
                session['count2'] = session['count2'] - 5
        rows = db.execute("select * from transactions order by time desc limit 5  offset ?", session['count2'])
        if len(rows) != 0:
            tran_accounts = []
            for entity in rows:
                account = {}
                account['id'] = entity['id']
                account['time'] = entity['time']
                account['product_id'] = entity['product_id']
                account['quantity'] = entity['quantity']
                account['total_price'] = entity['total_price']
                account['emp_id'] = entity['emp_id']
                account['type'] = entity['type']
                tran_accounts.append(account)
        else:
            tran_accounts = 'None'
        return render_template("acc_manager.html", error=request.args.get('error'), accounts=accounts, tran_accounts=tran_accounts)



@app.route("/logistics", methods=["GET", "POST"])
@login_required
def logistics():
    row = db.execute("select * from users where id=?",session["user_id"])
    if row[0]['type'] != 'logistics' and row[0]['type'] != 'admin':
        return redirect("/")
    if row[0]['type'] == 'admin':
        session['admin'] = 1
    if request.method == "POST":
        if "add_product" in request.form:
            if not request.form.get("name"):
                return render_template("logistics.html", error='product name')
            elif not request.form.get("drug_name"):
                return render_template("logistics.html", error='drug name')
            elif not request.form.get("cost_price"):
                return render_template("logistics.html", error='cost_price')
            elif not request.form.get("sell_price"):
                return render_template("logistics.html", error='sell_price')
            elif not request.form.get("expiration"):
                return render_template("logistics.html", error='expiration')

            rows = db.execute("select * from product where product_name=? and expiration=?",request.form.get("name"),request.form.get("expiration"))

            if len(rows) != 0:
                return render_template("logistics.html", error='product name and date')

            db.execute("insert into product(product_name, drug_name, description, manufacturer,cost_price,sell_price,quantity,expiration) values(?,?,?,?,?,?,?,?)", request.form.get("name"), request.form.get("drug_name"), request.form.get("description"), request.form.get("manu"), request.form.get("cost_price"), request.form.get("sell_price"), 0, request.form.get("expiration"))
        elif "purchase_stock" in request.form:
            if not request.form.get("product_id"):
                return render_template("logistics.html", error='product id')
            elif not request.form.get("quantity"):
                return render_template("logistics.html", error='quantity')
            elif not request.form.get("cost_price"):
                return render_template("logistics.html", error='cost_price')

            rows = db.execute("select * from product where id=?", int(request.form.get("product_id")))

            if len(rows) == 0:
                return render_template("logistics.html", error='product id not found')

            total = int(int(float(request.form.get("cost_price"))) * int(request.form.get("quantity")))
            row2 = db.execute("select * from account order by time desc limit 1")

            if total > row2[0]['net']:
                return render_template("logistics.html", error='insufficent funds')

            db.execute("insert into transactions(product_id, quantity, total_price, emp_id, type) values(?,?,?,?,?)", request.form.get("product_id"), request.form.get("quantity"), total, session['user_id'], "stock_purchase")
            db.execute("insert into account(type, amount, net) values(?,?,?)", "stock_purchase", total, row2[0]['net'] - total)
            db.execute("update product set quantity=?, cost_price=? where id=?", int(rows[0]['quantity']) + int(request.form.get("quantity")), request.form.get("cost_price"), int(request.form.get("product_id")))

        return redirect("/logistics")
    else:
        if not request.args.get("more") and not request.args.get("less"):
            session['count'] = 0
        elif request.args.get("more"):
            session['count'] = session['count'] + 5
        elif request.args.get("less"):
            if  session['count'] >= 0 and session['count'] <= 5:
                session['count'] = 0
            else:
                session['count'] = session['count'] - 5
        rows = db.execute("select * from product order by expiration limit 5  offset ?", session['count'])
        if len(rows) != 0:
            accounts = []
            for entity in rows:
                account = {}
                account['id'] = entity['id']
                account['product_name'] = entity['product_name']
                account['drug_name'] = entity['drug_name']
                account['description'] = entity['description']
                account['cost_price'] = entity['cost_price']
                account['sell_price'] = entity['sell_price']
                account['quantity'] = entity['quantity']
                account['expiration'] = entity['expiration']
                accounts.append(account)
        else:
            accounts = 'None'
        return render_template("logistics.html", error=request.args.get('error'), accounts=accounts)


@app.route("/sales", methods=["GET", "POST"])
@login_required
def sales():
    row = db.execute("select * from users where id=?",session["user_id"])
    if row[0]['type'] != 'sales' and row[0]['type'] != 'admin' and row[0]['type'] != 'pharmacy':
        return redirect("/")
    if row[0]['type'] == 'admin':
        session['admin'] = 1
    if request.method == "POST":
        if "checkout" in request.form:
            for item in session['cart']:
                total = int(float(item['sell_price'])) * int(item['quantity'])
                rows = db.execute("select * from product where id=?", int(item['id']))

                if len(rows) == 0:
                    return render_template("sales.html", error='product id not found')

                if int(rows[0]['quantity']) < int(item['quantity']):
                    return render_template("sales.html", error='low inventory')
                db.execute("insert into transactions(product_id, quantity, total_price, emp_id, type) values(?,?,?,?,?)", item['id'], item['quantity'], total, session['user_id'], "pos")


                db.execute("update product set quantity=? where id=?", int(rows[0]['quantity']) - int(item['quantity']), int(item['id']))

            row2 = db.execute("select * from account order by time desc limit 1")
            db.execute("insert into account(type, amount, net) values(?,?,?)", "pos", session['overall'], row2[0]['net'] + session['overall'])
            session['cart'] = []
            session['search'] = []
        elif "add_item" in request.form:
            if 'cart' not in session:
                session['cart'] = []
            if 'overall' not in session:
                session['overall'] = 0
            if int(request.form.get("quantity")) <= 0:
                return render_template("sales.html", error='quantity')

            for item in session['cart']:
                if int(item['id']) == int(request.form.get("id")):
                    item['quantity'] = int(item['quantity']) + int(request.form.get("quantity"))
                    item['total'] = int(item['total']) + int(request.form.get("quantity")) * int(float(request.form.get("sell_price")))
                    overall = 0
                    for entity in session['cart']:
                        overall += entity['total']

                    session['overall'] = overall
                    return redirect("/sales")
            item = {}
            item['id'] = request.form.get("id")
            item['quantity'] = request.form.get("quantity")
            item['product_name'] = request.form.get("product_name")
            item['drug_name'] = request.form.get("drug_name")
            item['sell_price'] = request.form.get("sell_price")
            item['total'] = int(request.form.get("quantity")) * int(float(request.form.get("sell_price")))

            session['cart'].append(item)

        elif "remove_item" in request.form:
            if not request.form.get("id"):
                return render_template("sales.html", error='id')

            for item in session['cart']:
                if request.form.get("id") == item['id']:
                    session['cart'].remove(item)
                    break
        elif "plus" in request.form:
            for item in session['cart']:
                if int(item['id']) == int(request.form.get("id")):
                    item['quantity'] = int(item['quantity']) + 1
                    item['total'] = int(item['total']) + int(float(request.form.get("sell_price")))
                    break
        elif "minus" in request.form:
            for item in session['cart']:
                if int(item['id']) == int(request.form.get("id")):
                    item['quantity'] = int(item['quantity']) - 1
                    item['total'] = int(item['total']) - int(float(request.form.get("sell_price")))
                    break
        overall = 0
        for entity in session['cart']:
            overall += entity['total']

        session['overall'] = overall
        return redirect("/sales")

    else:
        if request.args.get("name"):
            if not request.args.get("name"):
                return render_template("sales.html", error='product name')
            elif not request.args.get("drug_name"):
                return render_template("sales.html", error='drug name')

            rows = db.execute("select * from product where product_name like ? or drug_name like ?", "%" + request.args.get("name") + "%", "%" + request.args.get("drug_name") + "%" )

            if len(rows) != 0:
                products=[]
                for row in rows:
                    product = {}
                    product['id'] = row['id']
                    product['product_name'] = row['product_name']
                    product['drug_name'] = row['drug_name']
                    product['description'] = row['description']
                    product['manufacturer'] = row['manufacturer']
                    product['sell_price'] = row['sell_price']
                    products.append(product)
            else:
                products = 'None'
            session['search'] = products
            return render_template("sales.html", error=request.args.get('error'))
        elif request.args.get("clear_search"):
            session['search'] = []
            return render_template("sales.html", error=request.args.get('error'))
        elif request.args.get("clear_cart"):
            session['cart'] = []
            return render_template("sales.html", error=request.args.get('error'))
        else:
            return render_template("sales.html", error=request.args.get('error'))

@app.route("/admin", methods=["GET", "POST"])
@login_required
def admin():
    row = db.execute("select * from users where id=?",session["user_id"])
    if row[0]['type'] != 'admin':
        return redirect("/")
    if request.method == "POST":
        if "generate_report" in request.form:
            if not request.form.get("startdate"):
                return render_template("admin.html", error='startdate')
            elif not request.form.get("enddate"):
                return render_template("admin.html", error='enddate')
            start = request.form.get("startdate")
            end = request.form.get("enddate")
            startdate = datetime.strptime(start, "%Y-%m-%d").date()
            enddate = datetime.strptime(end, "%Y-%m-%d").date()


            if startdate >= enddate:
                return render_template("admin.html", error='date order')

            if startdate >= date.today() or enddate > date.today():
                return render_template("admin.html", error='inccorect date')
            session['dates']=[startdate, enddate]
            rows = db.execute("select * from transactions")

            results = []
            for row in rows:
                result = {}
                time = datetime.strptime(row['time'].split()[0], "%Y-%m-%d").date()
                if time >= startdate and time <= enddate:
                    result['id'] = row['id']
                    result['product_id'] = row['product_id']
                    result['time'] = row['time'].split()[0]
                    result['quantity'] = int(row['quantity'])
                    result['total_price'] = row['total_price']
                    result['emp_id'] = row['emp_id']
                    result['type'] = row['type']
                    results.append(result)

            #total_sales
            total_sales = 0
            for result in results:
                if result['type'] == 'pos':
                    total_sales += int(result['total_price'])

            session['total_sales'] = total_sales

            #best-seller
            best_seller = []
            product = []
            for result in results:
                item={}
                if result['product_id'] not in product:
                    item['product_id'] = result['product_id']
                    row6 = db.execute("select * from product where id=?",result['product_id'])
                    item['product_name'] = row6[0]['product_name']
                    item['quantity'] = 0
                    best_seller.append(item)
                    product.append(result['product_id'])

            for result in results:
                for entity in best_seller:
                    if entity['product_id'] == result['product_id']:
                        entity['quantity'] += 1
                        break
            x = []
            for entity in best_seller:
               x.append(entity['quantity'])
            x.sort(reverse=True)
            final = []
            max = round(len(x) / 2)
            y = x[round(0.75 * len(x)):]
            z = x[:max]
            for i in z:
                for entity in best_seller:
                        item = {}
                        if entity['quantity'] == i:
                            item['product_id'] = entity['product_id']
                            item['product_name'] = entity['product_name']
                            item['quantity'] = entity['quantity']
                            final.append(item)
            product_quantity = best_seller
            session['best_seller'] = final

            #worst_seller
            final = []
            for i in y:
                for entity in best_seller:
                        item = {}
                        if entity['quantity'] == i:
                            item['product_id'] = entity['product_id']
                            item['product_name'] = entity['product_name']
                            item['quantity'] = entity['quantity']
                            final.append(item)
            session['worst_seller'] = final
            #best-employee
            best_seller = []
            emp = []
            for result in results:
                item={}
                if result['emp_id'] not in emp:
                    item['emp_id'] = result['emp_id']
                    item['quantity'] = 0
                    best_seller.append(item)
                    emp.append(result['emp_id'])


            for result in results:
                for entity in best_seller:

                    if entity['emp_id'] == result['emp_id']:
                        entity['quantity'] += 1
                        break
            x = []
            for entity in best_seller:
               x.append(entity['quantity'])
            x.sort(reverse=True)
            final = []
            max = round(len(x) / 2)
            x = x[:max]
            for i in x:
                for entity in best_seller:
                        item = {}
                        if entity['quantity'] == i:
                            item['emp_id'] = entity['emp_id']
                            row7 = db.execute("select * from users where id=?", entity['emp_id'])
                            item['emp_name'] = row7[0]['name']
                            item['quantity'] = entity['quantity']
                            final.append(item)
            session['best_emp'] = final


            #profit
            total_cost = 0
            for item in product_quantity:
                row5 = db.execute("select * from product where id=?", int(item['product_id']))
                if len(row5) == 0:
                    return render_template("admin.html", error="product error")
                cost = float(item['quantity']) * float(row5[0]['cost_price'])
                total_cost += int(cost)
            session['profit'] = session['total_sales'] - total_cost

            product_profit = []
            for item in product_quantity:
                entity = {}
                row5 = db.execute("select * from product where id=?", int(item['product_id']))
                if len(row5) == 0:
                    return render_template("admin.html", error="product error")
                cost = int(float(item['quantity']) * float(row5[0]['cost_price']))
                sale = int(float(item['quantity']) * float(row5[0]['sell_price']))
                print(sale)
                entity['product_name'] = row5[0]['product_name']
                entity['product_id'] = int(item['product_id'])
                entity['product_profit'] = sale - cost
                product_profit.append(entity)

            session['product_profit'] = product_profit



        return redirect("/admin")
    else:
        return render_template("admin.html", error=request.args.get('error'))

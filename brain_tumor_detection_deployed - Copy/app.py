from flask import Flask, render_template, request
import load
import model
import os

app = Flask(__name__)

@app.route("/")
def hello():
    return render_template("index.html")

@app.route("/sub", methods=['POST'])
def check():
    ALLOWED_EXTENSIONS = {'png', 'jpg', 'jpeg', 'gif'}
    if request.method == "POST":
        if 'file' not in request.files:
            return render_template("index.html", message="No file part")
        file = request.files['file']
        print(file.filename)
        if file.filename == '':
            return render_template("index.html", message="No selected file")
        if file.filename.rsplit('.', 1)[1].lower() not in ALLOWED_EXTENSIONS:
            return render_template("index.html", message="File type not accpeted")
    
        if os.path.exists("./bestmodel.h5"):
            result = load.load(file)
        else:
            model.train()
            result = load.load(file)

        if result[0] > 0.5:
            s = "Tumor detected with certainty of " + str(round(result[0],4))
        else:
            s = "No Tumor detected with certainty of " + str(round((1-result[0]), 4))
        return render_template("check.html", message=s)

if __name__ == "__main__":
    app.run(debug=True)
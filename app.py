from flask import Flask, render_template, request
import requests
from bs4 import BeautifulSoup
import os
import xlsxwriter  # Import XlsxWriter

app = Flask(__name__)

@app.route("/", methods=["GET", "POST"])
def index():
    if request.method == "POST":
        instructor_name = request.form.get("instructor_name")
        results = search_google_scholar(instructor_name)

        # Export results to Excel (for draft purposes)
        if results:
            file_path = export_to_excel(results, instructor_name)
            return render_template("index.html", results=results, excel_file=file_path)
        else:
            return render_template("index.html", results=None, excel_file=None)

    return render_template("index.html", results=None, excel_file=None)

def search_google_scholar(instructor_name):
    # Define the URL for Google Scholar search
    url = f"https://scholar.google.com/scholar?q={instructor_name.replace(' ', '+')}"

    # Send an HTTP GET request to the Google Scholar URL
    response = requests.get(url)

    # Check if the request was successful (status code 200)
    if response.status_code == 200:
        # Parse the HTML content of the response using BeautifulSoup
        soup = BeautifulSoup(response.text, "html.parser")

        # Extract research paper details (simplified example)
        results = []
        for entry in soup.find_all("div", class_="gs_ri"):
            title = entry.find("h3").text
            authors = entry.find("div", class_="gs_a").text
            date = entry.find("div", class_="gs_rs").text
            results.append({"title": title, "authors": authors, "date": date})

        return results
    else:
        print("Failed to retrieve data from Google Scholar.")
        return []

def export_to_excel(results, instructor_name):
    if results:
        # Create a Pandas DataFrame from the results
        file_path = f"{instructor_name}_research_results.xlsx"
        
        # Create a new Excel workbook and add a worksheet
        workbook = xlsxwriter.Workbook(file_path)
        worksheet = workbook.add_worksheet("Research Results")

        # Define Excel formatting
        bold = workbook.add_format({'bold': True})
        
        # Write headers
        worksheet.write('A1', 'Title', bold)
        worksheet.write('B1', 'Authors', bold)
        worksheet.write('C1', 'Date', bold)
        
        # Write data
        for row, data in enumerate(results, start=1):
            worksheet.write(row, 0, data['title'])
            worksheet.write(row, 1, data['authors'])
            worksheet.write(row, 2, data['date'])

        # Close the workbook
        workbook.close()

        # Return the file path
        return file_path
    else:
        return None

if __name__ == "__main__":
    app.run(debug=True)

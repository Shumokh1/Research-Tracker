from flask import Flask, render_template, request, send_file
import requests
from bs4 import BeautifulSoup
import xlsxwriter
import re
import os

app = Flask(__name__)

# Global variable to store search results
search_results = None

@app.route("/", methods=["GET", "POST"])
def index():
    global search_results
    if request.method == "POST":
        instructor_name = request.form.get("instructor_name")
        search_results = search_google_scholar(instructor_name)
        return render_template("search.html", results=search_results, excel_file=(search_results is not None))

    return render_template("search.html", results=None, excel_file=None)

@app.route('/download_excel/<instructor_name>')
def download_excel(instructor_name):
    global search_results
    if search_results:
        file_path = export_to_excel(search_results, instructor_name)
        return send_file(file_path, as_attachment=True)
    else:
        return "No results to download", 404

def search_google_scholar(instructor_name):
    url = f"https://scholar.google.com/scholar?q={instructor_name.replace(' ', '+')}"

    response = requests.get(url)
    if response.status_code == 200:
        soup = BeautifulSoup(response.text, "html.parser")

        results = []
        for entry in soup.find_all("div", class_="gs_ri"):
            title = entry.find("h3").text if entry.find("h3") else "No Title"
            link = entry.find("a")["href"] if entry.find("a") else "#"

            authors, publication, year = "No Authors", "Not Available", ""
            authors_info = entry.find("div", class_="gs_a")
            if authors_info:
                info_text = authors_info.text

                # Extract authors (text before the first '-')
                authors = info_text.split(' - ')[0] if ' - ' in info_text else info_text

                # Attempt to find a year using a regular expression
                year_match = re.search(r'\b\d{4}\b', info_text)
                year = year_match.group(0) if year_match else ""

                # Extract publication (text between '-' and ', year')
                publication = info_text.split(' - ')[1].split(', ')[0] if ' - ' in info_text and year else ""

            results.append({"title": title, "authors": authors, "publication": publication, "date": year, "link": link})

        return results
    else:
        print("Failed to retrieve data from Google Scholar.")
        return []


def export_to_excel(results, instructor_name):
    file_path = f"{instructor_name}_research_results.xlsx"
    
    workbook = xlsxwriter.Workbook(file_path)
    worksheet = workbook.add_worksheet("Research Results")

    bold = workbook.add_format({'bold': True})
    
    worksheet.write('A1', 'Title', bold)
    worksheet.write('B1', 'Authors', bold)
    worksheet.write('C1', 'Publication', bold)
    worksheet.write('D1', 'Date', bold)
    worksheet.write('E1', 'Link', bold)
    
    for row, data in enumerate(results, start=1):
        worksheet.write(row, 0, data['title'])
        worksheet.write(row, 1, data['authors'])
        worksheet.write(row, 2, data['publication'])
        worksheet.write(row, 3, data['date'])
        worksheet.write(row, 4, data['link'])

    workbook.close()
    return file_path

if __name__ == "__main__":
    app.run(debug=True)

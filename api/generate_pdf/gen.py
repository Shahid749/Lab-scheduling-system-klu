import pandas
from sys import argv
import pdfkit
from random import choice
from datetime import timedelta, datetime
from os import remove
pdfkit.configuration(wkhtmltopdf="C:\\Program Files\\wkhtmltopdf\\bin\\wkhtmltopdf.exe") 
def removeAllFiles():
    try:
                remove(f'../../uploads/{uploadId}-hall-data.xlsx')
                remove(f'../../uploads/{uploadId}-internal-faculty.xlsx')
                remove(f'../../uploads/{uploadId}-external-faculty.xlsx')
    except: pass

uploadId = argv[1]
headers = {
    "hall-data": ["HALL NO", "AVAILABLE EXAM DATE", "AVAILABLE EXAM TIMING SLOTS", "TOTAL SEAT"],
    "internal-faculty": ["INTERNAL EXAMINER", 'FACULTY CODE', 'COURSE CODE', 'COURSE NAME', 'STUDENTS REGISTER NUMBER'],
    "external-faculty": ['EXTERNAL EXAMINER', 'FACULTY CODE']
}

for file_type in ["hall-data", "internal-faculty", "external-faculty"]:
    file_path = f'../../uploads/{uploadId}-{file_type}.xlsx'
    data = pandas.read_excel(file_path)
    for column in headers[file_type]:
        if column not in data.columns.tolist():
            print(f"failed|Can't find column name : [ {column.upper()} ] in {file_type.replace('-', ' ').title()} File")
            removeAllFiles()
            quit()
            
hall_data = pandas.read_excel( f'../../uploads/{uploadId}-hall-data.xlsx')
internal_faculty_data = pandas.read_excel( f'../../uploads/{uploadId}-internal-faculty.xlsx')
external_faculty_data = pandas.read_excel( f'../../uploads/{uploadId}-external-faculty.xlsx')

exam_slots = {
    'S1': '9:00 AM - 12:00 PM',
    'S2': '12:00 PM - 3:00 PM',
    'S3': '3:00 PM - 6:00 PM'
}

assigned_dates_slots = set()
exam_assignments = []
try:
    for index, faculty_row in internal_faculty_data.iterrows():
        internal_examiner = faculty_row['INTERNAL EXAMINER']
        faculty_code = faculty_row['FACULTY CODE']
        course_code = faculty_row['COURSE CODE']
        course_name = faculty_row['COURSE NAME']
        students_register_number = faculty_row['STUDENTS REGISTER NUMBER']
        shuffled_halls = hall_data.sample(frac=1)
        external_examiner = choice(external_faculty_data['EXTERNAL EXAMINER'])
        external_faculty_code = choice(external_faculty_data['FACULTY CODE'])
        for _, hall_row in shuffled_halls.iterrows():
            hall_no = hall_row['HALL NO']
            available_exam_date = hall_row['AVAILABLE EXAM DATE'].replace(" ", "")
            available_slots = hall_row['AVAILABLE EXAM TIMING SLOTS'].split(',')
            [start_date, end_date] = available_exam_date.split('-')
            available_dates = [date for date in pandas.date_range(start=pandas.to_datetime(start_date, format="%d/%m/%Y"), end=pandas.to_datetime(end_date, format="%d/%m/%Y")).strftime("%d/%m/%Y") if (date, hall_no) not in assigned_dates_slots]
            available_slots = [slot for slot in available_slots if (slot, hall_no) not in assigned_dates_slots]
            if not available_dates or not available_slots:
                continue
            selected_slot = choice(available_slots)
            selected_date = choice(available_dates)
            assigned_dates_slots.add((selected_date, hall_no))
            exam_assignments.append({
                'HALL NO': hall_no,
                'DATE': selected_date,
                'SLOT': selected_slot,
                'INTERNAL EXAMINER': internal_examiner,
                'EXTERNAL EXAMINER': external_examiner,  
                'FACULTY CODE': faculty_code,
                'EXTERNAL FACULTY CODE': external_faculty_code,
                'COURSE CODE': course_code,
                'COURSE NAME': course_name,
                'STUDENTS REGISTER NUMBER': students_register_number
            })
            break  
except Exception as e:
    print(f"failure|{e}")
    quit()

exam_assignments = sorted(exam_assignments, key=lambda x: x['DATE'])
template_table_string = ""
sno = 1
for exam in exam_assignments:
    [hall_no, exam_date, exam_slot, internal_examiner, external_examiner, faculty_code, external_faculty_code, course_code, course_name, regs] = exam.values()
    exam_time = exam_slots[exam_slot]
    no_of_students = len(regs.split(","))
    template_table_string += f"""
                <table class="exam-table">
                    <tr class='thead'>
                        <td><p class="s2">S.No</p></td>
                        <td><p class="s2">Course Code</p></td>
                        <td><p class="s2">Course Name</p></td>
                        <td><p class="s2">Venue Name</p></td>
                        <td><p class="s2">Exam Date</p></td>
                        <td><p class="s2">Exam Time</p></td>
                        <td><p class="s2">No of Students</p></td>
                    </tr>
                    <tr>
                        <td><p class="s3">{sno}</p></td>
                        <td><p class="s3">{course_code}</p></td>
                        <td><p class="s3">{course_name}</p></td>
                        <td><p class="s3">{hall_no}</p></td>
                        <td><p class="s3">{exam_date}</p></td>
                        <td><p class="s3">{exam_time}</p></td>
                        <td><p class="s3">{no_of_students}</p></td>
                    </tr>
                    <tr>
                        <td colspan="7">
                            <p class="s2">Internal Examiner: <span class="s3">{faculty_code} - {internal_examiner}, </span>External
                                Examiner: <span class="s3">{external_faculty_code} - {external_examiner}</span></p>
                            <p class="s2">Register Nos: <span class="s3">{regs.replace(" , ", ", ")}</span></p></td>
                    </tr>
                </table>
    """
    sno += 1
exam_date_month = datetime.strptime(exam_assignments[0]['DATE'], "%d/%m/%Y")
exam_next_month = exam_date_month + timedelta(days=30)
exam_month = f"{exam_date_month.strftime('%b').upper()}/{exam_next_month.strftime('%b').upper()}"
exam_date_year = datetime.strptime(exam_assignments[0]['DATE'], "%d/%m/%Y").strftime("%Y")
template_string = open("../../data/template.html", "r").read().replace("{{table_replace}}", template_table_string).replace("{{exam_months}}", exam_month).replace("{{exam_year}}", exam_date_year)

output_file_name = f'generated/{datetime.today().strftime("%d-%m-%Y")}-{uploadId}.pdf' 
pdfkit.from_string(template_string, "../../"+output_file_name)
removeAllFiles()
print(f"success|{output_file_name}")
from PyPDF2 import PdfFileWriter, PdfFileReader, utils
import sys
from io import BytesIO
import subprocess

def decompress_pdf(temp_buffer):
    temp_buffer.seek(0)  # Make sure we're at the start of the file.

    process = subprocess.Popen(['C:\\Users\\Administrator\\Desktop\\libooc-lumen\\bin\\PDFtk\\bin\\pdftk.exe',
                                '-',  # Read from stdin.
                                'output',
                                '-',  # Write to stdout.
                                'uncompress'],
                                stdin=temp_buffer,
                                stdout=subprocess.PIPE,
                                stderr=subprocess.PIPE,shell=True)
    stdout, stderr = process.communicate()

    return BytesIO(stdout)
# 开始页
start_page = int(sys.argv[1])

# 截止页
end_page = int(sys.argv[2])



output = PdfFileWriter()
with open(sys.argv[3], 'rb') as input_file:
    input_buffer = BytesIO(input_file.read())

try:
    pdf_file = PdfFileReader(input_buffer)
except utils.PdfReadError:
    pdf_file = PdfFileReader(decompress_pdf(open(sys.argv[3], 'rb')))
pdf_pages_len = pdf_file.getNumPages()

for i in range(start_page, end_page):
    output.addPage(pdf_file.getPage(i))

outputStream = open(sys.argv[4], "wb")
output.write(outputStream)
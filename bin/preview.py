from PyPDF2 import PdfFileWriter, PdfFileReader, utils
import sys
from cStringIO import StringIO
import subprocess

def decompress_pdf(temp_buffer):
    temp_buffer.seek(0)  # Make sure we're at the start of the file.

    process = subprocess.Popen(['pdftk.exe',
                                '-',  # Read from stdin.
                                'output',
                                '-',  # Write to stdout.
                                'uncompress'],
                                stdin=temp_buffer,
                                stdout=subprocess.PIPE,
                                stderr=subprocess.PIPE)
    stdout, stderr = process.communicate()

    return StringIO(stdout)
# 开始页
start_page = int(sys.argv[1])

# 截止页
end_page = int(sys.argv[2])



output = PdfFileWriter()
with open(sys.argv[3], 'rb') as input_file:
    input_buffer = StringIO(input_file.read())

try:
    pdf_file = PdfFileReader(input_buffer)
except utils.PdfReadError:
    pdf_file = PdfFileReader(decompress_pdf(input_file))
pdf_pages_len = pdf_file.getNumPages()

for i in range(start_page, end_page):
    output.addPage(pdf_file.getPage(i))

outputStream = open(sys.argv[4], "wb")
output.write(outputStream)
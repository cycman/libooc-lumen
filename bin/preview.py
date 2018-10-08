from PyPDF2 import PdfFileWriter, PdfFileReader
import sys

# 开始页
start_page = int(sys.argv[1])

# 截止页
end_page = int(sys.argv[2])

output = PdfFileWriter()
pdf_file = PdfFileReader(open(sys.argv[3], "rb"))
pdf_pages_len = pdf_file.getNumPages()

for i in range(start_page, end_page):
    output.addPage(pdf_file.getPage(i))

outputStream = open(sys.argv[4], "wb")
output.write(outputStream)
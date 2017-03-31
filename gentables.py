import os
import requests
import xlsxwriter
import collections
import better_exceptions

from jinja2 import Environment, FileSystemLoader

better_exceptions.MAX_LENGTH = None
THIS_DIR = os.path.dirname(os.path.abspath(__file__))
times = [
    '6:00 am',
    '6:15 am',
    '6:30 am',
    '6:45 am',
    '7:00 am',
    '7:15 am',
    '7:30 am',
    '7:45 am',
    '8:00 am',
    '8:15 am',
    '8:30 am',
    '8:45 am',
    '9:00 am',
    '9:15 am',
    '9:30 am',
    '9:45 am',
    '10:00 am',
    '10:15 am',
    '10:30 am',
    '10:45 am',
    '11:00 am',
    '11:15 am',
    '11:30 am',
    '11:45 am',
    '12:00 pm',
    '12:15 pm',
    '12:30 pm',
    '12:45 pm',
    '1:00 pm',
    '1:15 pm',
    '1:30 pm',
    '1:45 pm',
    '2:00 pm',
    '2:15 pm',
    '2:30 pm',
    '2:45 pm',
    '3:00 pm',
    '3:15 pm',
    '3:30 pm',
    '3:45 pm',
    '4:00 pm',
    '4:15 pm',
    '4:30 pm',
    '4:45 pm',
    '5:00 pm',
    '5:15 pm',
    '5:30 pm',
    '5:45 pm',
    '6:00 pm',
    '6:15 pm',
    '6:30 pm',
    '6:45 pm',
    '7:00 pm',
    '7:15 pm',
    '7:30 pm',
    '7:45 pm',
]


def gen_sheet():
    r = requests.get('https://api.sug.rocks/ccnschedule.json')
    cont = r.json()

    j2_env = Environment(loader=FileSystemLoader(os.path.join(THIS_DIR)), trim_blocks=True)
    j2_env.globals['len'] = len

    cont = collections.OrderedDict(sorted(cont.items()))

    # Generate page
    j2_env.get_template('grid.tpl').stream(days=cont, times=times)\
        .dump(os.path.join(THIS_DIR, 'grid.html'))

    workbook = xlsxwriter.Workbook(os.path.join(THIS_DIR, 'schedule.xlsx'))
    worksheet = workbook.add_worksheet()

    cell_format = workbook.add_format({'bold': True, 'font_size': 20})
    worksheet.merge_range(0, 1, 0, 7, 'CARTOON NETWORK SCHEDULE', cell_format)

    row = 2
    col = 0

    cell_format = workbook.add_format({'bold': True, 'align': 'right'})
    for time in times:
        worksheet.write(row, col, time, cell_format)
        row += 1

    row = 1
    col = 1

    for day in cont:
        if day == '_':
            continue

        cell_format = workbook.add_format({'bold': True, 'align': 'center'})
        worksheet.write(row, col, day, cell_format)

        row += 1
        for slot in cont[day]:
            name = slot['show']
            if name == 'MOVIE' or name == 'SPECIAL:':
                name = slot['title']

            cell_format = workbook.add_format({
                'align': 'center',
                'valign': 'vcenter',
                'fg_color': slot['color']
            })

            if slot['slots'] > 1:
                nrow = row + slot['slots'] - 1
                worksheet.merge_range(row, col, nrow, col, name, cell_format)
            else:
                worksheet.write(row, col, name, cell_format)

            row += slot['slots']

        row = 1
        col += 1

    worksheet.set_column(1, 50, 24.2)
    worksheet.freeze_panes(2, 1)
    workbook.close()


if __name__ == '__main__':
    gen_sheet()

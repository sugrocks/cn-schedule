# CN Schedule
[![Standard - JavaScript Style Guide](https://img.shields.io/badge/code%20style-standard-green.svg)](http://standardjs.com/)
[![Gitgud Build Status](https://gitgud.io/sug/cn-schedule/badges/master/build.svg)](https://gitgud.io/sug/cn-schedule/commits/master)


> [cn.sug.rocks](https://cn.sug.rocks/)


### Install

- `git clone https://gitgud.io/sug/cn-schedule.git`
- `yarn`
- `npm run build`
- Point webserver to `dist/`


And set a crontab to run `grid/gentables.py` and copy the `index.html` from there to `dist/grid/`.
<template>
  <div class="home content">
    <h2>Welcome!</h2>
    <p>
      <b>Select a date to see its schedule.</b><br/>
      You can change between the two sources or to the stats with the tabs above the schedules.
    </p>
    <p>
      <svg xmlns="http://www.w3.org/2000/svg"
        width="24" height="24" viewBox="0 0 24 24"
        fill="none" stroke="currentColor" stroke-width="2"
        stroke-linecap="round" stroke-linejoin="round"
        class="feather feather-calendar">
        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
        <line x1="16" y1="2" x2="16" y2="6"></line>
        <line x1="8" y1="2" x2="8" y2="6"></line>
        <line x1="3" y1="10" x2="21" y2="10"></line>
      </svg> Change the date range.<br/>

      <svg xmlns="http://www.w3.org/2000/svg"
        width="24" height="24" viewBox="0 0 24 24"
        fill="none" stroke="currentColor" stroke-width="2"
        stroke-linecap="round" stroke-linejoin="round"
        class="feather feather-grid">
        <rect x="3" y="3" width="7" height="7"></rect>
        <rect x="14" y="3" width="7" height="7"></rect>
        <rect x="14" y="14" width="7" height="7"></rect>
        <rect x="3" y="14" width="7" height="7"></rect>
      </svg> Switch to the grid view.<br/>

      <svg xmlns="http://www.w3.org/2000/svg"
        width="24" height="24" viewBox="0 0 24 24"
        fill="none" stroke="currentColor" stroke-width="2"
        stroke-linecap="round" stroke-linejoin="round"
        class="feather feather-settings">
        <circle cx="12" cy="12" r="3"></circle>
        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
      </svg> Settings and about this website.
    </p>
    <p>
      <b>Notes:</b><br/>
      - By default, time is EST. You can change that in the settings to use your local timezone.<br/>
      &nbsp; <small>(You'll get some small delays when changing days or opening the grid view.)</small><br/>
      - We try to use colors as close as the ones on the CN Schedule Archive. If a show's missing in our database, it will generate a temporary color.<br/>
      - The data is provided "as-is". CN's infos comes from an unused page from 2004, Zap2it's and TVGuide's from their "hidden" API.
    </p>
  </div>
</template>

<script>
export default {
  name: 'Home',
  metaInfo () {
    return {
      title: 'Home'
    }
  }
}
</script>

<style lang="scss" scoped>
@import '../assets/colors';

.home {
  background-color: $line-even;
  padding: 25px;

  svg {
    height: 18px;
    vertical-align: text-bottom;
    width: 18px;
  }
}

@media screen and (min-width: 675px) {
  .home {
    margin-left: 200px;
  }
}
</style>

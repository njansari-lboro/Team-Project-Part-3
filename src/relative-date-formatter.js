/**
 * Formats a date using relative date formatting.
 *
 * @param {Date} date A date object to be relatively formatted.
 *
 * @return {string} Returns the given date relatively formatted as a string.
 *
 * @example
 * const date1 = new Date("2024-02-18 10:30:21")
 * const relativeDate1 = formatRelativeDate(date1)
 * // "5 minutes ago"
 *
 * const date2 = new Date("2024-02-15 21:09:26")
 * const relativeDate2 = formatRelativeDate(date2)
 * // "2 days ago"
 *
 * const date3 = new Date("2024-01-05 04:16:52")
 * const relativeDate3 = formatRelativeDate(date3)
 * // "1 month ago"
 */
function formatRelativeDate(date) {
    const components = getRelativeDateComponents(date)

    const locale = navigator.language
    const relative = new Intl.RelativeTimeFormat(locale)

    if (components.seconds <= 1) {
        return "Now"
    } else if (components.seconds < 60) {
        // Within a minute
        return relative.format(-components.seconds, "seconds")
    } else if (components.minutes < 60) {
        // Within an hour
        return relative.format(-components.minutes, "minutes")
    } else if (components.hours < 24) {
        // Within a day
        return relative.format(-components.hours, "hours")
    } else if (components.days < 7) {
        // Within a week
        return relative.format(-components.days, "days")
    } else if (components.weeks < 4) {
        // Within a month
        return relative.format(-components.weeks, "weeks")
    } else if (components.months < 12) {
        // Within a year
        return relative.format(-components.months, "months")
    } else {
        // Over a year ago
        return relative.format(-components.years, "years")
    }
}

function getRelativeDateComponents(date) {
    const userTimeZoneOffset = new Date().getTimezoneOffset()
    const timeZoneOffsetMillis = -userTimeZoneOffset * 60 * 1000
    const offsetDate = new Date(date.getTime() + timeZoneOffsetMillis)

    const now = new Date()
    const diff = now - offsetDate

    const seconds = Math.floor(diff / 1000)
    const minutes = Math.floor(seconds / 60)
    const hours = Math.floor(minutes / 60)
    const days = Math.floor(hours / 24)
    const weeks = Math.floor(days / 7)
    const months = Math.floor(weeks / 4)
    const years = Math.floor(months / 12)

    return {
        date: offsetDate,
        now: now,
        seconds: seconds,
        minutes: minutes,
        hours: hours,
        days: days,
        weeks: weeks,
        months: months,
        years: years
    }
}

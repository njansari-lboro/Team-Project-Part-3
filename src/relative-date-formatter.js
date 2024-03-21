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
    const locale = navigator.language

    const now = new Date()
    const diff = now - date

    const seconds = Math.floor(diff / 1000)
    const minutes = Math.floor(seconds / 60)
    const hours = Math.floor(minutes / 60)
    const days = Math.floor(hours / 24)
    const weeks = Math.floor(days / 7)
    const months = Math.floor(weeks / 4)
    const years = Math.floor(months / 12)

    const relative = new Intl.RelativeTimeFormat(locale)
    if (seconds <= 1) {
        return "Now"
    } else if (seconds < 60) {
        // Within a minute
        return relative.format(-seconds, "seconds")
    } else if (minutes < 60) {
        // Within an hour
        return relative.format(-minutes, "minutes")
    } else if (hours < 24) {
        // Within a day
        return relative.format(-hours, "hours")
    } else if (days < 7) {
        // Within a week
        return relative.format(-days, "days")
    } else if (weeks < 4) {
        // Within a month
        return relative.format(-weeks, "weeks")
    } else if (months < 12) {
        // Within a year
        return relative.format(-months, "months")
    } else {
        // Over a year ago
        return relative.format(-years, "years")
    }
}

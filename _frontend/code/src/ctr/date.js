class CtrDate {
    get_name($date, $format = "F d, Y H:i:s") {
        const d = new Date($date);

        let hours24 = d.getHours();
        let hours12 = hours24 % 12 || 12;
        let ampm = hours24 < 12 ? "AM" : "PM";

        const map = {
            F: d.toLocaleString("en-US", { month: "long" }), // Full month
            d: String(d.getDate()).padStart(2, "0"),         // Day with leading zero
            Y: d.getFullYear(),                              // Full year
            H: String(hours24).padStart(2, "0"),             // 24h hour
            h: String(hours12).padStart(2, "0"),             // 12h hour
            i: String(d.getMinutes()).padStart(2, "0"),      // Minutes
            s: String(d.getSeconds()).padStart(2, "0"),      // Seconds
            A: ampm,                                         // AM/PM
            a: ampm.toLowerCase()                            // am/pm
        };

        return $format.replace(/F|d|Y|H|h|i|s|A|a/g, m => map[m]);
    }

    change_date(date, interval) {
        const d = new Date(date);

        if (interval) {
            // mag gamit:  like "+20 minutes", "-2 days", "+1 hour"
            const match = interval.match(/^([+-]?\d+)\s*(year|month|day|hour|minute|second)s?$/i);
            if (match) {
                const value = parseInt(match[1], 10);
                const unit = match[2].toLowerCase();

                switch (unit) {
                    case "year":
                        d.setFullYear(d.getFullYear() + value);
                        break;
                    case "month":
                        d.setMonth(d.getMonth() + value);
                        break;
                    case "day":
                        d.setDate(d.getDate() + value);
                        break;
                    case "hour":
                        d.setHours(d.getHours() + value);
                        break;
                    case "minute":
                        d.setMinutes(d.getMinutes() + value);
                        break;
                    case "second":
                        d.setSeconds(d.getSeconds() + value);
                        break;
                }
            }
        }

        const pad = n => String(n).padStart(2, "0");
        return (
            d.getFullYear() +
            "-" + pad(d.getMonth() + 1) +
            "-" + pad(d.getDate()) +
            " " + pad(d.getHours()) +
            ":" + pad(d.getMinutes()) +
            ":" + pad(d.getSeconds())
        );
    }


    get_date(date, format = "Y-m-d H:i:s") {
        const d = new Date(date);

        let hours24 = d.getHours();
        let hours12 = hours24 % 12 || 12;
        let ampm = hours24 < 12 ? "AM" : "PM";

        const map = {
            Y: d.getFullYear(),
            m: String(d.getMonth() + 1).padStart(2, "0"),
            d: String(d.getDate()).padStart(2, "0"),
            H: String(hours24).padStart(2, "0"),
            h: String(hours12).padStart(2, "0"),
            i: String(d.getMinutes()).padStart(2, "0"),
            s: String(d.getSeconds()).padStart(2, "0"),
            A: ampm,
            a: ampm.toLowerCase()
        };

        return format.replace(/Y|m|d|H|h|i|s|A|a/g, m => map[m]);
    }

    now(format = "Y-m-d H:i:s") {
        const d = new Date();

        let hours24 = d.getHours();
        let hours12 = hours24 % 12 || 12;
        let ampm = hours24 < 12 ? "AM" : "PM";

        const map = {
            Y: d.getFullYear(),
            m: String(d.getMonth() + 1).padStart(2, "0"),
            d: String(d.getDate()).padStart(2, "0"),
            H: String(hours24).padStart(2, "0"),
            h: String(hours12).padStart(2, "0"),
            i: String(d.getMinutes()).padStart(2, "0"),
            s: String(d.getSeconds()).padStart(2, "0"),
            A: ampm,
            a: ampm.toLowerCase()
        };

        return format.replace(/Y|m|d|H|h|i|s|A|a/g, m => map[m]);
    }

    dateInterval(date1, date2, type = "days") {
        const start = new Date(date1);
        const end = new Date(date2);
        const diffMs = end - start;
        const diffSeconds = diffMs / 1000;
        const diffMinutes = diffSeconds / 60;
        const diffHours = diffMinutes / 60;
        const diffDays = diffHours / 24;

        switch (type.toLowerCase()) {
            case "second":
            case "seconds":
                return diffSeconds;

            case "minute":
            case "minutes":
                return diffMinutes;

            case "hour":
            case "hours":
                return diffHours;

            case "day":
            case "days":
                return diffDays;

            case "week":
            case "weeks":
                return diffDays / 7;

            case "month":
            case "months": {
                const years = end.getFullYear() - start.getFullYear();
                const months = years * 12 + (end.getMonth() - start.getMonth());
                const dayAdjust = end.getDate() - start.getDate();
                return months + (dayAdjust / 30);
            }

            case "year":
            case "years": {
                const years = end.getFullYear() - start.getFullYear();
                const monthDiff = end.getMonth() - start.getMonth();
                const dayDiff = end.getDate() - start.getDate();
                return years + (monthDiff / 12) + (dayDiff / 365);
            }

            default:
                throw new Error("Invalid interval type: " + type);
        }
    }

    intervalName(date1, date2 = new Date()) {
        const start = new Date(date1);
        const end = new Date(date2);

        const diffMs = end - start;
        const isFuture = diffMs < 0;
        const diffSeconds = Math.abs(diffMs) / 1000;

        let value, unit;

        if (diffSeconds < 60) {
            value = Math.floor(diffSeconds);
            unit = "second";
        } else if (diffSeconds < 3600) {
            value = Math.floor(diffSeconds / 60);
            unit = "minute";
        } else if (diffSeconds < 86400) {
            value = Math.floor(diffSeconds / 3600);
            unit = "hour";
        } else if (diffSeconds < 2592000) {
            value = Math.floor(diffSeconds / 86400);
            unit = "day";
        } else if (diffSeconds < 31536000) {
            value = Math.floor(diffSeconds / 2592000);
            unit = "month";
        } else {
            value = Math.floor(diffSeconds / 31536000);
            unit = "year";
        }

        const plural = value !== 1 ? "s" : "";
        const suffix = isFuture ? "left" : "ago";

        return `${value} ${unit}${plural} ${suffix}`;
    }
}

const CtrDATE = new CtrDate();

if (typeof window !== "undefined") {
    window.CtrDATE = CtrDATE;
}

if (typeof module !== "undefined" && typeof module.exports !== "undefined") {
    module.exports = CtrDATE;
}

export default CtrDATE;
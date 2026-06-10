/**
 * Lightweight date/number formatting helpers used across Patient pages.
 * All functions are null-safe — pass null/undefined to get '—'.
 */

/** Format a date string as "12 Jan 2025" */
export function fmtDate(val) {
    if (!val) return '—';
    return new Date(val).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
}

/** Format a date string as "Jan 2025" */
export function fmtMonthYear(val) {
    if (!val) return '—';
    return new Date(val).toLocaleDateString('en-GB', { month: 'short', year: 'numeric' });
}

/** Format a datetime string as "12 Jan 2025, 14:30" */
export function fmtDateTime(val) {
    if (!val) return '—';
    return new Date(val).toLocaleString('en-GB', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit', hour12: false });
}

/** Format a time string (HH:MM or HH:MM:SS) as "2:30 PM" */
export function fmtTime(val) {
    if (!val) return '—';
    try {
        return new Date('1970-01-01T' + val).toLocaleTimeString('en', { hour: 'numeric', minute: '2-digit' });
    } catch {
        return val;
    }
}

/** "3 hours ago" / "2 days ago" style relative time */
export function fmtRelative(val) {
    if (!val) return '';
    const diff = Date.now() - new Date(val).getTime();
    const minutes = Math.floor(diff / 60000);
    if (minutes < 1)   return 'just now';
    if (minutes < 60)  return `${minutes} minute${minutes !== 1 ? 's' : ''} ago`;
    const hours = Math.floor(minutes / 60);
    if (hours < 24)    return `${hours} hour${hours !== 1 ? 's' : ''} ago`;
    const days = Math.floor(hours / 24);
    if (days < 30)     return `${days} day${days !== 1 ? 's' : ''} ago`;
    const months = Math.floor(days / 30);
    if (months < 12)   return `${months} month${months !== 1 ? 's' : ''} ago`;
    const years = Math.floor(months / 12);
    return `${years} year${years !== 1 ? 's' : ''} ago`;
}

/** "in 3 days" / "2 months" duration-until style */
export function fmtUntil(val) {
    if (!val) return '';
    const diff = new Date(val).getTime() - Date.now();
    const days = Math.ceil(diff / 86400000);
    if (days <= 0)     return 'expired';
    if (days === 1)    return '1 day';
    if (days < 30)     return `${days} days`;
    const months = Math.round(days / 30);
    return `${months} month${months !== 1 ? 's' : ''}`;
}

/** Format a number as Nigerian Naira with commas */
export function fmtNaira(val) {
    if (val == null) return '—';
    return Number(val).toLocaleString('en-NG');
}

/** Extract year from a date string */
export function fmtYear(val) {
    if (!val) return '—';
    return new Date(val).getFullYear();
}

/** Map Prescription/LabResult/Allergy status to pill colour key */
export function statusColor(status) {
    const map = {
        active: 'green', chronic: 'amber', inactive: 'grey', discontinued: 'grey',
        pending: 'amber', approved: 'green', denied: 'red',
        high: 'red', low: 'amber', critical: 'red', normal: 'green',
        mild: 'amber', moderate: 'amber', severe: 'red',
    };
    return map[(status || '').toLowerCase()] || 'grey';
}

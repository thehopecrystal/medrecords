import { router } from '@inertiajs/react';
import { useState } from 'react';
import AppLayout from '@/layouts/AppLayout';
import { fmtDateTime } from '@/utils/fmt';

function pillColor(action = '') {
    if (action.includes('break_glass')) return { background: 'var(--color-red-lt)', color: 'var(--color-red)' };
    if (action.includes('consent'))     return { background: 'var(--color-navy-lt)', color: 'var(--color-navy)' };
    if (action.includes('dispense'))    return { background: 'var(--color-amber-lt)', color: 'var(--color-amber)' };
    return { background: 'var(--color-border)', color: 'var(--color-text-2)' };
}

function inputStyle() {
    return { width: '100%', padding: '8px 10px', borderRadius: 8, border: '1.5px solid var(--color-border)', fontSize: 13, background: 'var(--color-card)', color: 'var(--color-text)', boxSizing: 'border-box' };
}

export default function AuditLog({ logs, actions }) {
    const params = new URLSearchParams(typeof window !== 'undefined' ? window.location.search : '');
    const [filters, setFilters] = useState({
        action: params.get('action') || '',
        actor:  params.get('actor')  || '',
        from:   params.get('from')   || '',
        to:     params.get('to')     || '',
    });

    function applyFilters(e) {
        e.preventDefault();
        const query = {};
        if (filters.action) query.action = filters.action;
        if (filters.actor)  query.actor  = filters.actor;
        if (filters.from)   query.from   = filters.from;
        if (filters.to)     query.to     = filters.to;
        router.get('/patient/audit-log', query, { preserveState: true });
    }

    function clearFilters() {
        setFilters({ action: '', actor: '', from: '', to: '' });
        router.get('/patient/audit-log');
    }

    const hasFilters = filters.action || filters.actor || filters.from || filters.to;

    return (
        <AppLayout title="Who Saw My Record">
            <div style={{ marginBottom: 24 }}>
                <h1 style={{ fontSize: 22, fontWeight: 800, color: 'var(--color-navy)', margin: 0 }}>Access History</h1>
                <p style={{ fontSize: 13, color: 'var(--color-text-2)', marginTop: 4 }}>Every access to your medical record is logged here</p>
            </div>

            {/* Filter bar */}
            <div style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 12, padding: 16, marginBottom: 20 }}>
                <form onSubmit={applyFilters} style={{ display: 'grid', gridTemplateColumns: '1fr 1fr 1fr 1fr auto', gap: 12, alignItems: 'flex-end' }}>
                    <div>
                        <label style={{ display: 'block', fontSize: 12, fontWeight: 600, color: 'var(--color-text-2)', marginBottom: 5 }}>Action type</label>
                        <select style={{ ...inputStyle(), cursor: 'pointer' }} value={filters.action} onChange={e => setFilters(f => ({ ...f, action: e.target.value }))}>
                            <option value="">All actions</option>
                            {(actions || []).map(a => <option key={a} value={a}>{a}</option>)}
                        </select>
                    </div>
                    <div>
                        <label style={{ display: 'block', fontSize: 12, fontWeight: 600, color: 'var(--color-text-2)', marginBottom: 5 }}>Person</label>
                        <input style={inputStyle()} type="text" value={filters.actor} onChange={e => setFilters(f => ({ ...f, actor: e.target.value }))} placeholder="Name…" />
                    </div>
                    <div>
                        <label style={{ display: 'block', fontSize: 12, fontWeight: 600, color: 'var(--color-text-2)', marginBottom: 5 }}>From date</label>
                        <input style={inputStyle()} type="date" value={filters.from} onChange={e => setFilters(f => ({ ...f, from: e.target.value }))} />
                    </div>
                    <div>
                        <label style={{ display: 'block', fontSize: 12, fontWeight: 600, color: 'var(--color-text-2)', marginBottom: 5 }}>To date</label>
                        <input style={inputStyle()} type="date" value={filters.to} onChange={e => setFilters(f => ({ ...f, to: e.target.value }))} />
                    </div>
                    <div style={{ display: 'flex', gap: 8 }}>
                        <button type="submit" style={{ background: 'var(--color-navy)', color: '#fff', border: 'none', borderRadius: 8, padding: '8px 16px', fontSize: 12, fontWeight: 600, cursor: 'pointer', whiteSpace: 'nowrap' }}>Filter</button>
                        <button type="button" onClick={clearFilters} style={{ background: 'var(--color-card)', color: 'var(--color-text-2)', border: '1.5px solid var(--color-border)', borderRadius: 8, padding: '8px 14px', fontSize: 12, fontWeight: 600, cursor: 'pointer', whiteSpace: 'nowrap' }}>Clear</button>
                    </div>
                </form>
            </div>

            {/* Table */}
            <div style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 12, overflow: 'hidden' }}>
                <div style={{ overflowX: 'auto' }}>
                    <table style={{ width: '100%', borderCollapse: 'collapse' }}>
                        <thead>
                            <tr style={{ background: 'var(--color-border)' }}>
                                {['Action', 'By', 'Description', 'When'].map(h => (
                                    <th key={h} style={{ padding: '10px 16px', fontSize: 12, fontWeight: 700, textAlign: 'left', color: 'var(--color-text-2)', textTransform: 'uppercase', letterSpacing: '0.04em' }}>{h}</th>
                                ))}
                            </tr>
                        </thead>
                        <tbody>
                            {logs.data && logs.data.length > 0 ? logs.data.map((log, i) => (
                                <tr key={log.id} style={{ borderTop: '1px solid var(--color-border)', background: i % 2 === 0 ? 'transparent' : 'rgba(0,0,0,0.01)' }}>
                                    <td style={{ padding: '12px 16px' }}>
                                        <span style={{ display: 'inline-flex', alignItems: 'center', fontSize: 11, fontWeight: 600, padding: '2px 8px', borderRadius: 20, ...pillColor(log.action) }}>
                                            {log.action}
                                        </span>
                                    </td>
                                    <td style={{ padding: '12px 16px', fontSize: 13 }}>{log.actor?.name}</td>
                                    <td style={{ padding: '12px 16px', fontSize: 13, maxWidth: 380 }}>{log.description}</td>
                                    <td style={{ padding: '12px 16px', fontSize: 12, color: 'var(--color-text-3)', whiteSpace: 'nowrap' }}>{fmtDateTime(log.created_at)}</td>
                                </tr>
                            )) : (
                                <tr>
                                    <td colSpan={4} style={{ textAlign: 'center', color: 'var(--color-text-2)', padding: 24, fontSize: 13 }}>
                                        No access history{hasFilters ? ' matching your filters.' : '.'}
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>
                {logs.links && logs.links.length > 3 && (
                    <div style={{ padding: '14px 22px', borderTop: '1px solid var(--color-border)', display: 'flex', gap: 6 }}>
                        {logs.links.map((link, i) => (
                            <button key={i} onClick={() => link.url && router.get(link.url, {}, { preserveState: true })} disabled={!link.url} dangerouslySetInnerHTML={{ __html: link.label }} style={{ padding: '5px 10px', borderRadius: 6, border: '1px solid var(--color-border)', fontSize: 12, cursor: link.url ? 'pointer' : 'default', background: link.active ? 'var(--color-navy)' : 'var(--color-card)', color: link.active ? '#fff' : 'var(--color-text-2)', fontWeight: link.active ? 700 : 400 }} />
                        ))}
                    </div>
                )}
            </div>
        </AppLayout>
    );
}

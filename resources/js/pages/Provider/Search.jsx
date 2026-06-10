import { useState } from 'react';
import { Link, router, usePage } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';

export default function Search({ patients, q }) {
    const [query, setQuery] = useState(q ?? '');

    function handleSubmit(e) {
        e.preventDefault();
        router.get('/provider/search', { q: query }, { preserveState: true });
    }

    return (
        <AppLayout title="Patient Lookup">
            <div style={{ marginBottom: 20 }}>
                <h1 style={{ fontSize: 22, fontWeight: 800, color: 'var(--color-navy)', margin: 0 }}>Patient Lookup</h1>
                <p style={{ fontSize: 13, color: 'var(--color-text-3)', marginTop: 4 }}>Search by MedID, NIN, phone, or name</p>
            </div>

            {/* Search form */}
            <div style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 12, padding: '16px 20px', marginBottom: 20 }}>
                <form onSubmit={handleSubmit} style={{ display: 'flex', gap: 12 }}>
                    <input
                        type="text"
                        value={query}
                        onChange={e => setQuery(e.target.value)}
                        placeholder="Search by MedID, name, phone, or email..."
                        autoFocus
                        style={{ flex: 1, background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 8, padding: '10px 14px', fontSize: 14, color: 'var(--color-text)', outline: 'none' }}
                    />
                    <button type="submit" style={{ background: 'var(--color-navy)', color: '#fff', padding: '10px 20px', borderRadius: 8, fontWeight: 600, fontSize: 13, border: 'none', cursor: 'pointer' }}>
                        Search
                    </button>
                </form>
            </div>

            {/* Results */}
            {(q?.length ?? 0) >= 2 && (
                <>
                    {patients.length > 0 ? (
                        <div style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 12, overflow: 'hidden' }}>
                            <table style={{ width: '100%', borderCollapse: 'collapse' }}>
                                <thead>
                                    <tr style={{ borderBottom: '1px solid var(--color-border)' }}>
                                        {['Name', 'MedID', 'DOB / Age', 'Phone', 'Action'].map(h => (
                                            <th key={h} style={{ padding: '11px 18px', textAlign: 'left', fontSize: 11, fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.05em', color: 'var(--color-text-3)' }}>{h}</th>
                                        ))}
                                    </tr>
                                </thead>
                                <tbody>
                                    {patients.map(p => (
                                        <tr key={p.id} style={{ borderBottom: '1px solid var(--color-border)' }}>
                                            <td style={{ padding: '12px 18px', fontWeight: 600, fontSize: 13 }}>{p.user?.name}</td>
                                            <td style={{ padding: '12px 18px' }}>
                                                <span style={{ fontFamily: 'monospace', fontSize: 12 }}>{p.med_id}</span>
                                            </td>
                                            <td style={{ padding: '12px 18px', fontSize: 13, color: 'var(--color-text-2)' }}>
                                                {p.dob
                                                    ? `${new Date(p.dob).toLocaleDateString('en-NG', { day: '2-digit', month: 'short', year: 'numeric' })} (${p.age} yrs)`
                                                    : '—'}
                                            </td>
                                            <td style={{ padding: '12px 18px', fontSize: 13, color: 'var(--color-text-2)' }}>{p.user?.phone}</td>
                                            <td style={{ padding: '12px 18px' }}>
                                                <Link href={`/provider/patients/${p.id}`} style={{ background: 'var(--color-navy)', color: '#fff', padding: '6px 14px', borderRadius: 6, fontSize: 12, fontWeight: 600, textDecoration: 'none' }}>
                                                    View record
                                                </Link>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    ) : (
                        <div style={{ display: 'flex', alignItems: 'center', gap: 10, padding: '12px 16px', borderRadius: 10, background: 'var(--color-navy-lt)', color: 'var(--color-navy)', fontSize: 13, borderLeft: '3px solid var(--color-navy)' }}>
                            <span>i</span>
                            <span>No patients found for "{q}".</span>
                        </div>
                    )}
                </>
            )}
        </AppLayout>
    );
}

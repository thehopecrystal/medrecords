import { Link } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';

function Pill({ color, children }) {
    const colors = {
        navy:  { background: 'var(--color-navy-lt)',  color: 'var(--color-navy)' },
        green: { background: 'var(--color-green-lt)', color: 'var(--color-green)' },
        grey:  { background: '#F1F5F9', color: '#64748B' },
    };
    return (
        <span style={{ ...(colors[color] ?? colors.grey), fontSize: 11, fontWeight: 700, padding: '2px 8px', borderRadius: 10, display: 'inline-block' }}>
            {children}
        </span>
    );
}

export default function Facilities({ facilities }) {
    const { data, links, current_page, last_page } = facilities;

    return (
        <AppLayout title="Facilities">
            <div style={{ marginBottom: 24 }}>
                <h1 style={{ fontSize: 22, fontWeight: 800, color: 'var(--color-navy)', margin: 0 }}>Facilities</h1>
                <p style={{ fontSize: 13, color: 'var(--color-text-2)', marginTop: 4 }}>All registered facilities on MedRecords</p>
            </div>

            <div style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 14, overflow: 'hidden' }}>
                <div style={{ overflowX: 'auto' }}>
                    <table style={{ width: '100%', borderCollapse: 'collapse', fontSize: 13 }}>
                        <thead>
                            <tr style={{ background: '#F8FAFC' }}>
                                {['Name','Type','City','License','Providers','Status'].map(h => (
                                    <th key={h} style={{ padding: '11px 16px', textAlign: 'left', fontSize: 11, fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.05em', color: 'var(--color-text-2)', borderBottom: '1px solid var(--color-border)', whiteSpace: 'nowrap' }}>{h}</th>
                                ))}
                            </tr>
                        </thead>
                        <tbody>
                            {data.length === 0 ? (
                                <tr><td colSpan={6} style={{ textAlign: 'center', color: 'var(--color-text-3)', padding: 24 }}>No facilities registered.</td></tr>
                            ) : data.map(f => (
                                <tr key={f.id} style={{ borderBottom: '1px solid var(--color-border)' }}>
                                    <td style={{ padding: '12px 16px' }}><strong>{f.name}</strong></td>
                                    <td style={{ padding: '12px 16px' }}><Pill color="navy">{f.type}</Pill></td>
                                    <td style={{ padding: '12px 16px', color: 'var(--color-text-2)' }}>{f.city}, {f.state}</td>
                                    <td style={{ padding: '12px 16px', fontFamily: 'monospace', fontSize: 12 }}>{f.license_no || '—'}</td>
                                    <td style={{ padding: '12px 16px' }}>{f.providers_count}</td>
                                    <td style={{ padding: '12px 16px' }}>
                                        <Pill color={f.is_active ? 'green' : 'grey'}>{f.is_active ? 'Active' : 'Inactive'}</Pill>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>

                {/* Pagination */}
                {last_page > 1 && (
                    <div style={{ padding: '14px 16px', borderTop: '1px solid var(--color-border)', display: 'flex', gap: 6, flexWrap: 'wrap' }}>
                        {links.map((link, i) => (
                            <Link
                                key={i}
                                href={link.url ?? '#'}
                                style={{
                                    padding: '5px 10px', borderRadius: 6, fontSize: 12, fontWeight: 600,
                                    textDecoration: 'none',
                                    background: link.active ? 'var(--color-navy)' : 'var(--color-card)',
                                    color: link.active ? '#fff' : 'var(--color-text-2)',
                                    border: '1px solid var(--color-border)',
                                    pointerEvents: link.url ? 'auto' : 'none',
                                    opacity: link.url ? 1 : 0.4,
                                }}
                                dangerouslySetInnerHTML={{ __html: link.label }}
                            />
                        ))}
                    </div>
                )}
            </div>
        </AppLayout>
    );
}

import { Link, router } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';

export default function FacilitiesImport() {
    function handleSubmit(e) {
        e.preventDefault();
        const fd = new FormData(e.target);
        router.post('/admin/facilities/import', fd, { forceFormData: true });
    }

    return (
        <AppLayout title="Bulk Facility Import">
            <div style={{ display: 'flex', alignItems: 'flex-start', justifyContent: 'space-between', marginBottom: 24, gap: 12 }}>
                <div>
                    <h1 style={{ fontSize: 22, fontWeight: 800, color: 'var(--color-navy)', margin: 0 }}>Bulk Import Facilities</h1>
                    <p style={{ fontSize: 13, color: 'var(--color-text-2)', marginTop: 4 }}>Upload a CSV to add multiple facilities at once.</p>
                </div>
                <Link href="/admin/facilities" style={{ padding: '8px 16px', borderRadius: 8, border: '1.5px solid var(--color-border)', background: 'var(--color-card)', color: 'var(--color-text-2)', fontSize: 13, fontWeight: 600, textDecoration: 'none' }}>← Back to Facilities</Link>
            </div>

            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 20, maxWidth: 960 }}>
                {/* Upload form */}
                <div style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 14, overflow: 'hidden' }}>
                    <div style={{ padding: '16px 20px 12px', borderBottom: '1px solid var(--color-border)' }}>
                        <div style={{ fontWeight: 700, fontSize: 14, color: 'var(--color-navy)' }}>Upload CSV</div>
                    </div>
                    <div style={{ padding: '20px' }}>
                        <form onSubmit={handleSubmit} encType="multipart/form-data">
                            <div style={{ marginBottom: 20 }}>
                                <label style={{ display: 'block', fontSize: 11, fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.06em', color: 'var(--color-text-2)', marginBottom: 5 }}>CSV File *</label>
                                <input
                                    type="file"
                                    name="csv_file"
                                    accept=".csv,.txt"
                                    required
                                    style={{ background: '#fff', padding: 10, border: '2px dashed var(--color-border)', borderRadius: 8, cursor: 'pointer', width: '100%', boxSizing: 'border-box', fontFamily: 'inherit', fontSize: 13 }}
                                />
                                <div style={{ fontSize: 11, color: 'var(--color-text-3)', marginTop: 4 }}>Max 2 MB. UTF-8 encoded. First row must be column headers.</div>
                            </div>

                            <div style={{ background: 'var(--color-navy-lt)', color: 'var(--color-navy)', borderLeft: '3px solid var(--color-navy)', borderRadius: 8, padding: '10px 14px', fontSize: 12, marginBottom: 16 }}>
                                ℹ️ Existing facilities (matched by name) will be skipped — no duplicates are created.
                            </div>

                            <button type="submit" style={{ padding: '10px 22px', borderRadius: 9, background: 'var(--color-navy)', color: '#fff', border: 'none', fontSize: 13, fontWeight: 700, cursor: 'pointer', fontFamily: 'inherit' }}>
                                Import Facilities
                            </button>
                        </form>
                    </div>
                </div>

                {/* CSV format guide */}
                <div style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 14, overflow: 'hidden' }}>
                    <div style={{ padding: '16px 20px 12px', borderBottom: '1px solid var(--color-border)' }}>
                        <div style={{ fontWeight: 700, fontSize: 14, color: 'var(--color-navy)' }}>CSV Format</div>
                    </div>
                    <div style={{ padding: '20px' }}>
                        <p style={{ fontSize: 13, color: 'var(--color-text-2)', marginBottom: 14 }}>
                            Your CSV must have at least a <code style={{ background: '#F1F5F9', padding: '2px 6px', borderRadius: 4, fontFamily: 'monospace', fontSize: 12 }}>name</code> column. All other columns are optional:
                        </p>
                        <div style={{ overflowX: 'auto' }}>
                            <table style={{ width: '100%', borderCollapse: 'collapse', fontSize: 12 }}>
                                <thead>
                                    <tr style={{ background: '#F8FAFC' }}>
                                        {['Column','Required','Example'].map(h => (
                                            <th key={h} style={{ padding: '8px 12px', textAlign: 'left', fontSize: 11, fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.05em', color: 'var(--color-text-2)', borderBottom: '1px solid var(--color-border)' }}>{h}</th>
                                        ))}
                                    </tr>
                                </thead>
                                <tbody>
                                    {[
                                        { col: 'name',    req: true,  ex: 'Lagos Island General Hospital' },
                                        { col: 'type',    req: false, ex: 'hospital / clinic / pharmacy' },
                                        { col: 'state',   req: false, ex: 'Lagos' },
                                        { col: 'city',    req: false, ex: 'Lagos Island' },
                                        { col: 'address', req: false, ex: '1 Marina Road' },
                                        { col: 'phone',   req: false, ex: '08012345678' },
                                        { col: 'email',   req: false, ex: 'info@hospital.ng' },
                                    ].map(row => (
                                        <tr key={row.col} style={{ borderBottom: '1px solid var(--color-border)' }}>
                                            <td style={{ padding: '8px 12px' }}><code style={{ background: '#F1F5F9', padding: '2px 5px', borderRadius: 4, fontFamily: 'monospace' }}>{row.col}</code></td>
                                            <td style={{ padding: '8px 12px' }}>
                                                {row.req
                                                    ? <span style={{ background: 'var(--color-red-lt)', color: 'var(--color-red)', fontSize: 10, fontWeight: 700, padding: '2px 7px', borderRadius: 10 }}>Required</span>
                                                    : <span style={{ color: 'var(--color-text-3)' }}>Optional</span>
                                                }
                                            </td>
                                            <td style={{ padding: '8px 12px', color: 'var(--color-text-2)' }}>{row.ex}</td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>

                        <div style={{ marginTop: 16 }}>
                            <div style={{ fontSize: 11, fontWeight: 700, textTransform: 'uppercase', color: 'var(--color-text-2)', marginBottom: 6 }}>Example CSV</div>
                            <pre style={{ background: '#F8FAFC', border: '1px solid var(--color-border)', borderRadius: 8, padding: 12, fontSize: 11, overflowX: 'auto', color: 'var(--color-text-2)', margin: 0 }}>
{`name,type,state,city
Lagos Island General,hospital,Lagos,Lagos Island
Eko Hospital,hospital,Lagos,Surulere
First Cardiology,clinic,Lagos,Victoria Island`}
                            </pre>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}

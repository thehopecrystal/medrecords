import AppLayout from '@/layouts/AppLayout';

export default function History({ dispenses }) {
    const data = dispenses?.data ?? dispenses ?? [];

    return (
        <AppLayout title="Dispense History">
            <div style={{ marginBottom: 24 }}>
                <h1 style={{ fontSize: 22, fontWeight: 800, color: 'var(--color-navy)', margin: 0 }}>Dispense History</h1>
                <p style={{ fontSize: 13, color: 'var(--color-text-3)', marginTop: 4 }}>All prescriptions you have dispensed</p>
            </div>

            <div style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 12, overflow: 'hidden' }}>
                <table style={{ width: '100%', borderCollapse: 'collapse' }}>
                    <thead>
                        <tr style={{ borderBottom: '1px solid var(--color-border)' }}>
                            {['Patient', 'Drug', 'Qty', 'Batch', 'Price', 'Payment', 'Date'].map(h => (
                                <th key={h} style={{ padding: '11px 18px', textAlign: 'left', fontSize: 11, fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.05em', color: 'var(--color-text-3)' }}>{h}</th>
                            ))}
                        </tr>
                    </thead>
                    <tbody>
                        {data.length === 0 ? (
                            <tr>
                                <td colSpan={7} style={{ textAlign: 'center', color: 'var(--color-text-3)', padding: 24, fontSize: 13 }}>No dispenses on record.</td>
                            </tr>
                        ) : (
                            data.map(d => (
                                <tr key={d.id} style={{ borderBottom: '1px solid var(--color-border)' }}>
                                    <td style={{ padding: '12px 18px' }}>
                                        <div style={{ fontWeight: 600, fontSize: 13 }}>{d.prescription?.patient?.user?.name}</div>
                                        <div style={{ fontSize: 11, color: 'var(--color-text-3)' }}>{d.prescription?.patient?.med_id}</div>
                                    </td>
                                    <td style={{ padding: '12px 18px', fontSize: 13 }}>{d.prescription?.drug_name} {d.prescription?.dose}</td>
                                    <td style={{ padding: '12px 18px', fontSize: 13 }}>{d.quantity}</td>
                                    <td style={{ padding: '12px 18px', fontSize: 12 }}>{d.batch_lot || '—'}</td>
                                    <td style={{ padding: '12px 18px', fontSize: 13 }}>{d.price ? 'NGN ' + Number(d.price).toLocaleString() : '—'}</td>
                                    <td style={{ padding: '12px 18px', fontSize: 12 }}>{d.payment_method || '—'}</td>
                                    <td style={{ padding: '12px 18px', fontSize: 12, color: 'var(--color-text-3)' }}>
                                        {d.created_at ? new Date(d.created_at).toLocaleDateString('en-NG', { day: '2-digit', month: 'short', year: 'numeric' }) : '—'}
                                    </td>
                                </tr>
                            ))
                        )}
                    </tbody>
                </table>
                {dispenses?.links && dispenses.links.length > 3 && (
                    <div style={{ padding: '14px 20px', borderTop: '1px solid var(--color-border)', fontSize: 12 }}>
                        {/* Pagination links handled server-side; link component can be added if needed */}
                        <span style={{ color: 'var(--color-text-3)' }}>Page {dispenses.current_page} of {dispenses.last_page}</span>
                    </div>
                )}
            </div>
        </AppLayout>
    );
}

import { usePage } from '@inertiajs/react';

function fmt(dateStr) {
    if (!dateStr) return '';
    return new Date(dateStr).toLocaleDateString('en-NG', { day: '2-digit', month: 'short', year: 'numeric' });
}

function fmtDateTime(dateStr) {
    if (!dateStr) return '';
    const d = new Date(dateStr);
    return d.toLocaleDateString('en-NG', { day: '2-digit', month: 'short', year: 'numeric' }) + ', ' +
           d.toLocaleTimeString('en-NG', { hour: '2-digit', minute: '2-digit' });
}

export default function EncounterPrint({ encounter }) {
    const { auth } = usePage().props;
    const patient  = encounter.patient ?? {};
    const allergies = (patient.allergies ?? []).filter(a => a.is_active);
    const prescriptions = encounter.prescriptions ?? [];

    const s = {
        page:        { fontFamily: '"Segoe UI", Arial, sans-serif', fontSize: 13, color: '#1A1F26', padding: 32, maxWidth: 800, margin: '0 auto' },
        header:      { display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', borderBottom: '2px solid #1E3A5F', paddingBottom: 14, marginBottom: 20 },
        brand:       { fontSize: 20, fontWeight: 800, color: '#1E3A5F' },
        brandSub:    { fontSize: 11, fontWeight: 400, color: '#6B7280' },
        meta:        { textAlign: 'right', fontSize: 12, color: '#6B7280' },
        banner:      { background: '#F3F4F6', padding: '12px 14px', borderRadius: 8, marginBottom: 20, display: 'grid', gridTemplateColumns: '1fr auto', gap: 16 },
        patName:     { fontSize: 18, fontWeight: 700, color: '#1E3A5F' },
        patMeta:     { fontSize: 12, color: '#6B7280', marginTop: 2 },
        allergyFlag: { display: 'inline-block', background: '#FBEAEC', color: '#B23A48', padding: '2px 8px', borderRadius: 6, fontSize: 11, fontWeight: 600, marginTop: 6, marginRight: 4 },
        section:     { marginBottom: 20 },
        secTitle:    { fontSize: 11, fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.05em', color: '#6B7280', borderBottom: '1px solid #E5E7EB', paddingBottom: 4, marginBottom: 10 },
        noteBox:     { background: '#F3F4F6', padding: '12px 14px', borderRadius: 6, lineHeight: 1.6, whiteSpace: 'pre-wrap' },
        row:         { display: 'flex', gap: 24, marginBottom: 8 },
        fieldLabel:  { fontSize: 11, fontWeight: 600, color: '#6B7280', textTransform: 'uppercase', letterSpacing: '0.04em' },
        fieldValue:  { marginTop: 2, lineHeight: 1.5 },
        sigRow:      { display: 'flex', justifyContent: 'space-between', marginTop: 32, paddingTop: 16, borderTop: '1px solid #E5E7EB', fontSize: 12, color: '#6B7280' },
        sigLine:     { borderBottom: '1px solid #aaa', width: 200, marginBottom: 4, height: 24 },
    };

    return (
        <>
            <style>{`
                @media print { .no-print { display: none !important; } body { padding: 0; } }
                * { box-sizing: border-box; margin: 0; padding: 0; }
                table.rx-table { width: 100%; border-collapse: collapse; font-size: 12px; }
                table.rx-table th, table.rx-table td { padding: 6px 8px; text-align: left; border-bottom: 1px solid #E5E7EB; }
                table.rx-table th { font-weight: 600; color: #6B7280; font-size: 11px; text-transform: uppercase; }
            `}</style>

            <div style={s.page}>
                {/* Print controls */}
                <div className="no-print" style={{ marginBottom: 20, display: 'flex', gap: 10 }}>
                    <button onClick={() => window.print()} style={{ background: '#1E3A5F', color: '#fff', border: 'none', padding: '9px 18px', borderRadius: 8, fontSize: 13, fontWeight: 600, cursor: 'pointer' }}>
                        Print / Save as PDF
                    </button>
                    <button onClick={() => history.back()} style={{ background: '#F3F4F6', color: '#3D4754', border: '1px solid #D1D5DB', padding: '9px 18px', borderRadius: 8, fontSize: 13, cursor: 'pointer' }}>
                        Back
                    </button>
                </div>

                {/* Header */}
                <div style={s.header}>
                    <div>
                        <div style={s.brand}>MedRecords <span style={s.brandSub}>— Clinical Encounter Summary</span></div>
                        <div style={{ fontSize: 12, color: '#6B7280', marginTop: 4 }}>{encounter.facility?.name} · {fmtDateTime(encounter.created_at)}</div>
                    </div>
                    <div style={s.meta}>
                        <div>Encounter type: <strong>{encounter.type ? encounter.type.charAt(0).toUpperCase() + encounter.type.slice(1) : ''}</strong></div>
                        <div>Status: <strong>{encounter.status}</strong></div>
                        {encounter.signed_at && <div>Signed: {fmtDateTime(encounter.signed_at)}</div>}
                    </div>
                </div>

                {/* Patient banner */}
                <div style={s.banner}>
                    <div>
                        <div style={s.patName}>{patient.user?.name}</div>
                        <div style={s.patMeta}>
                            {['MedID: ' + (patient.med_id ?? ''),
                              patient.dob ? 'DOB: ' + fmt(patient.dob) + ' (' + patient.age + ' yrs)' : null,
                              patient.gender ? patient.gender.charAt(0).toUpperCase() + patient.gender.slice(1) : null,
                              patient.blood_type || null,
                            ].filter(Boolean).join(' · ')}
                        </div>
                        {allergies.map((a, i) => (
                            <span key={i} style={s.allergyFlag}>! {a.allergen} ({a.severity})</span>
                        ))}
                    </div>
                    <div style={{ textAlign: 'right', fontSize: 12, color: '#6B7280' }}>
                        <div>Clinician: <strong>{encounter.provider?.name}</strong></div>
                    </div>
                </div>

                {encounter.chief_complaint && (
                    <div style={s.section}>
                        <div style={s.secTitle}>Chief complaint</div>
                        <div>{encounter.chief_complaint}</div>
                    </div>
                )}

                {encounter.vitals && (
                    <div style={s.section}>
                        <div style={s.secTitle}>Vital signs</div>
                        <div>{encounter.vitals}</div>
                    </div>
                )}

                {encounter.history && (
                    <div style={s.section}>
                        <div style={s.secTitle}>History of presenting illness</div>
                        <div style={s.noteBox}>{encounter.history}</div>
                    </div>
                )}

                {encounter.diagnosis && (
                    <div style={s.section}>
                        <div style={s.row}>
                            <div style={{ flex: 1 }}>
                                <div style={s.fieldLabel}>Diagnosis</div>
                                <div style={s.fieldValue}>{encounter.diagnosis}</div>
                            </div>
                            {encounter.icd10_code && (
                                <div style={{ flex: 1 }}>
                                    <div style={s.fieldLabel}>ICD-10</div>
                                    <div style={s.fieldValue}>{encounter.icd10_code}</div>
                                </div>
                            )}
                        </div>
                    </div>
                )}

                {encounter.assessment_plan && (
                    <div style={s.section}>
                        <div style={s.secTitle}>Assessment & plan</div>
                        <div style={s.noteBox}>{encounter.assessment_plan}</div>
                    </div>
                )}

                {encounter.notes && (
                    <div style={s.section}>
                        <div style={s.secTitle}>Clinical notes</div>
                        <div style={s.noteBox}>{encounter.notes}</div>
                    </div>
                )}

                {prescriptions.length > 0 && (
                    <div style={s.section}>
                        <div style={s.secTitle}>Prescriptions issued ({prescriptions.length})</div>
                        <table className="rx-table">
                            <thead>
                                <tr><th>Drug</th><th>Dose</th><th>Frequency</th><th>Duration</th><th>Rx ID</th></tr>
                            </thead>
                            <tbody>
                                {prescriptions.map(rx => (
                                    <tr key={rx.id}>
                                        <td><strong>{rx.drug_name}</strong></td>
                                        <td>{rx.dose}</td>
                                        <td>{rx.frequency}</td>
                                        <td>{rx.duration_days ? rx.duration_days + ' days' : '—'}</td>
                                        <td style={{ fontFamily: 'monospace', fontSize: 11 }}>{rx.rx_id}</td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                )}

                {/* Signature */}
                <div style={s.sigRow}>
                    <div>
                        <div style={s.sigLine}></div>
                        <div>{encounter.provider?.name}</div>
                        {auth?.user?.provider?.mdcn_folio && (
                            <div style={{ color: '#6B7280' }}>{auth.user.provider.mdcn_folio}</div>
                        )}
                    </div>
                    <div style={{ textAlign: 'right' }}>
                        <div style={{ color: '#2C5F2D', fontWeight: 700, fontSize: 12 }}>MedRecords — verified clinical document</div>
                        <div style={{ marginTop: 4 }}>Generated: {fmtDateTime(new Date().toISOString())}</div>
                    </div>
                </div>
            </div>
        </>
    );
}

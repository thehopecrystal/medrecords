import { Link, router } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';
import { fmtDate, fmtMonthYear, fmtYear, statusColor } from '@/utils/fmt';

const TABS = [
    { key: 'all', label: 'All' },
    { key: 'encounters', label: 'Encounters' },
    { key: 'prescriptions', label: 'Medications' },
    { key: 'labs', label: 'Labs' },
    { key: 'dental', label: 'Dental' },
    { key: 'eye', label: 'Eye Care' },
    { key: 'vaccines', label: 'Vaccines' },
    { key: 'allergies', label: 'Allergies & Conditions' },
];

const PILL_COLORS = {
    navy:   { background: 'var(--color-navy-lt)', color: 'var(--color-navy)' },
    green:  { background: 'var(--color-green-lt)', color: 'var(--color-green)' },
    amber:  { background: 'var(--color-amber-lt)', color: 'var(--color-amber)' },
    red:    { background: 'var(--color-red-lt)', color: 'var(--color-red)' },
    grey:   { background: 'var(--color-border)', color: 'var(--color-text-2)' },
};

function Pill({ color = 'grey', children, style = {} }) {
    return (
        <span style={{ display: 'inline-flex', alignItems: 'center', fontSize: 11, fontWeight: 600, padding: '2px 8px', borderRadius: 20, ...(PILL_COLORS[color] || PILL_COLORS.grey), ...style }}>
            {children}
        </span>
    );
}

function EmptyRow({ message }) {
    return <div style={{ padding: 16, color: 'var(--color-text-2)', fontSize: 13 }}>{message}</div>;
}

function SectionCard({ title, pill, children }) {
    return (
        <div style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 12, marginBottom: 20 }}>
            <div style={{ padding: '14px 20px', borderBottom: '1px solid var(--color-border)' }}>
                <div style={{ fontWeight: 700, fontSize: 14, display: 'flex', alignItems: 'center', gap: 8 }}>{title} {pill}</div>
            </div>
            {children}
        </div>
    );
}

export default function Record({ patient, tab = 'all' }) {
    const show = (key) => tab === 'all' || tab === key;

    function changeTab(key) {
        router.get('/patient/record', { tab: key }, { preserveState: true, replace: true });
    }

    return (
        <AppLayout title="My Medical Record">
            <div style={{ marginBottom: 16 }}>
                <h1 style={{ fontSize: 22, fontWeight: 800, color: 'var(--color-navy)', marginBottom: 4 }}>My Medical Record</h1>
                <p style={{ color: 'var(--color-text-2)', fontSize: 12 }}>All categories · MedID {patient.med_id}</p>
            </div>

            {/* Tab strip */}
            <div style={{ display: 'flex', gap: 4, flexWrap: 'wrap', marginBottom: 20, borderBottom: '1px solid var(--color-border)', paddingBottom: 0 }}>
                {TABS.map(t => (
                    <button key={t.key} onClick={() => changeTab(t.key)} style={{
                        padding: '8px 14px', fontSize: 13, fontWeight: tab === t.key ? 700 : 500,
                        color: tab === t.key ? 'var(--color-navy)' : 'var(--color-text-2)',
                        background: 'none', border: 'none', cursor: 'pointer',
                        borderBottom: tab === t.key ? '2px solid var(--color-navy)' : '2px solid transparent',
                        marginBottom: -1,
                    }}>
                        {t.label}
                    </button>
                ))}
            </div>

            {/* Allergy alert */}
            {patient.active_allergies?.length > 0 && (
                <div style={{ display: 'flex', gap: 8, alignItems: 'center', padding: '10px 14px', background: 'var(--color-red-lt)', border: '1px solid var(--color-red)', borderRadius: 8, marginBottom: 16, fontSize: 13 }}>
                    <span>⚠</span>
                    <span><strong>Allergy alert:</strong> {patient.active_allergies.map(a => a.allergen).join(', ')}</span>
                </div>
            )}

            <div style={{ display: 'grid', gridTemplateColumns: '1fr 300px', gap: 20 }}>
                <div>
                    {/* Encounters */}
                    {show('encounters') && (
                        <SectionCard title="Encounters & Visits" pill={<Pill color="navy">{patient.encounters?.length || 0}</Pill>}>
                            {patient.encounters?.length ? [...patient.encounters].sort((a, b) => new Date(b.created_at) - new Date(a.created_at)).map(enc => (
                                <div key={enc.id} style={{ padding: '14px 20px', borderBottom: '1px solid var(--color-border)' }}>
                                    <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', gap: 12 }}>
                                        <div>
                                            <div style={{ fontWeight: 600, fontSize: 14 }}>{enc.chief_complaint || enc.type}</div>
                                            <div style={{ fontSize: 12, color: 'var(--color-text-2)', marginTop: 2 }}>{enc.provider?.name} · {enc.facility?.name}</div>
                                        </div>
                                        <span style={{ fontSize: 12, color: 'var(--color-text-3)', whiteSpace: 'nowrap', flexShrink: 0 }}>{fmtDate(enc.created_at)}</span>
                                    </div>
                                    {enc.assessment_plan && <div style={{ marginTop: 8, fontSize: 13, color: 'var(--color-text-2)', lineHeight: 1.5 }}>{enc.assessment_plan.substring(0, 200)}</div>}
                                    {enc.diagnosis && <div style={{ marginTop: 4, fontSize: 12, color: 'var(--color-text-3)' }}>{enc.diagnosis}</div>}
                                </div>
                            )) : <EmptyRow message="No encounters on record." />}
                        </SectionCard>
                    )}

                    {/* Prescriptions */}
                    {show('prescriptions') && (
                        <SectionCard title="Prescriptions" pill={<Pill color="green">{patient.active_prescriptions?.length || 0} active</Pill>}>
                            {patient.prescriptions?.length ? [...patient.prescriptions].sort((a, b) => new Date(b.created_at) - new Date(a.created_at)).map(rx => (
                                <div key={rx.id} style={{ padding: '12px 20px', borderBottom: '1px solid var(--color-border)', display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                                    <div>
                                        <div style={{ fontWeight: 600, fontSize: 14 }}>
                                            <Link href={`/patient/prescription/${rx.id}`} style={{ color: 'inherit', textDecoration: 'none' }}>{rx.drug_name} {rx.dose}</Link>
                                        </div>
                                        <div style={{ fontSize: 12, color: 'var(--color-text-2)', marginTop: 2 }}>{rx.frequency} · {rx.prescribed_by?.name}</div>
                                    </div>
                                    <Pill color={rx.status_color || statusColor(rx.status)}>{rx.status}</Pill>
                                </div>
                            )) : <EmptyRow message="No prescriptions on record." />}
                        </SectionCard>
                    )}

                    {/* Lab Results */}
                    {show('labs') && (
                        <SectionCard title="Lab Results" pill={<Pill color="navy">{patient.lab_results?.length || 0}</Pill>}>
                            {patient.lab_results?.length ? [...patient.lab_results].sort((a, b) => new Date(b.collected_at) - new Date(a.collected_at)).map(lab => (
                                <div key={lab.id} style={{ padding: '12px 20px', borderBottom: '1px solid var(--color-border)' }}>
                                    <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', gap: 12 }}>
                                        <div>
                                            <div style={{ fontWeight: 600, fontSize: 14 }}>{lab.test_name}</div>
                                            <div style={{ fontSize: 12, color: 'var(--color-text-2)', marginTop: 2 }}>{fmtDate(lab.collected_at)}</div>
                                        </div>
                                        <Pill color={lab.flag_color || statusColor(lab.flag)}>{lab.result} {lab.unit} {lab.flag && <span style={{ marginLeft: 4 }}>{lab.flag}</span>}</Pill>
                                    </div>
                                    {lab.reference_range && <div style={{ marginTop: 4, fontSize: 11, color: 'var(--color-text-3)' }}>Ref: {lab.reference_range}</div>}
                                </div>
                            )) : <EmptyRow message="No lab results on record." />}
                        </SectionCard>
                    )}

                    {/* Vaccinations */}
                    {show('vaccines') && (
                        <SectionCard title="Vaccinations" pill={<Pill color="navy">{patient.vaccinations?.length || 0}</Pill>}>
                            {patient.vaccinations?.length ? [...patient.vaccinations].sort((a, b) => new Date(b.administered_at) - new Date(a.administered_at)).map(vax => (
                                <div key={vax.id} style={{ padding: '12px 20px', borderBottom: '1px solid var(--color-border)', display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                                    <div>
                                        <div style={{ fontWeight: 600, fontSize: 14 }}>{vax.vaccine_name}</div>
                                        <div style={{ fontSize: 12, color: 'var(--color-text-2)', marginTop: 2 }}>{fmtDate(vax.administered_at)} · {vax.administered_by?.name}</div>
                                    </div>
                                    {vax.next_due
                                        ? <Pill color={new Date(vax.next_due) < new Date() ? 'red' : 'amber'}>Due {fmtMonthYear(vax.next_due)}</Pill>
                                        : <Pill color="green">Up to date</Pill>
                                    }
                                </div>
                            )) : <EmptyRow message="No vaccination records." />}
                        </SectionCard>
                    )}

                    {/* Dental */}
                    {show('dental') && (
                        <SectionCard title="Dental Records" pill={<Pill color="navy">{patient.dental_records?.length || 0}</Pill>}>
                            {patient.dental_records?.length ? [...patient.dental_records].sort((a, b) => new Date(b.visit_date) - new Date(a.visit_date)).map(dr => (
                                <div key={dr.id} style={{ padding: '12px 20px', borderBottom: '1px solid var(--color-border)' }}>
                                    <div style={{ fontWeight: 600, fontSize: 14 }}>{dr.procedure || 'Dental visit'}</div>
                                    <div style={{ fontSize: 12, color: 'var(--color-text-2)', marginTop: 2 }}>{fmtDate(dr.visit_date)} · {dr.provider?.name}</div>
                                    {dr.notes && <div style={{ marginTop: 6, fontSize: 13, color: 'var(--color-text-2)' }}>{dr.notes.substring(0, 150)}</div>}
                                </div>
                            )) : <EmptyRow message="No dental records." />}
                        </SectionCard>
                    )}

                    {/* Eye Care */}
                    {show('eye') && (
                        <SectionCard title="Eye Care" pill={<Pill color="navy">{patient.eye_care_records?.length || 0}</Pill>}>
                            {patient.eye_care_records?.length ? [...patient.eye_care_records].sort((a, b) => new Date(b.exam_date) - new Date(a.exam_date)).map(ec => (
                                <div key={ec.id} style={{ padding: '12px 20px', borderBottom: '1px solid var(--color-border)' }}>
                                    <div style={{ fontWeight: 600, fontSize: 14 }}>Eye exam</div>
                                    <div style={{ fontSize: 12, color: 'var(--color-text-2)', marginTop: 2 }}>{fmtDate(ec.exam_date)} · {ec.provider?.name}</div>
                                    <div style={{ marginTop: 4, fontSize: 12, color: 'var(--color-text-2)' }}>
                                        OD: {ec.od_prescription} · OS: {ec.os_prescription}<br />
                                        VA: {ec.visual_acuity_od} / {ec.visual_acuity_os} · IOP: {ec.iop_od} / {ec.iop_os} mmHg
                                    </div>
                                </div>
                            )) : <EmptyRow message="No eye care records." />}
                        </SectionCard>
                    )}

                    {/* Allergies & Conditions */}
                    {show('allergies') && (
                        <SectionCard title="Allergies & Conditions">
                            {patient.allergies?.map(a => (
                                <div key={a.id} style={{ padding: '12px 20px', borderBottom: '1px solid var(--color-border)', display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                                    <div>
                                        <div style={{ fontWeight: 600, fontSize: 14 }}>{a.allergen}</div>
                                        <div style={{ fontSize: 12, color: 'var(--color-text-2)', marginTop: 2 }}>{a.reaction} · confirmed {fmtYear(a.confirmed_at)}</div>
                                    </div>
                                    <Pill color={a.severity_color || statusColor(a.severity)}>{a.severity}</Pill>
                                </div>
                            ))}
                            {patient.conditions?.map(c => (
                                <div key={c.id} style={{ padding: '12px 20px', borderBottom: '1px solid var(--color-border)', display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                                    <div>
                                        <div style={{ fontWeight: 600, fontSize: 14 }}>{c.name}</div>
                                        <div style={{ fontSize: 12, color: 'var(--color-text-2)', marginTop: 2 }}>{c.icd10_code} · diagnosed {fmtMonthYear(c.diagnosed_at)}</div>
                                    </div>
                                    <Pill color={c.status === 'active' || c.status === 'chronic' ? 'amber' : 'grey'}>{c.status}</Pill>
                                </div>
                            ))}
                            {!patient.allergies?.length && !patient.conditions?.length && <EmptyRow message="No allergies or conditions recorded." />}
                        </SectionCard>
                    )}
                </div>

                {/* Aside */}
                <aside>
                    <div style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 12, padding: 16, marginBottom: 16 }}>
                        <div style={{ fontSize: 11, color: 'var(--color-text-2)', textTransform: 'uppercase', letterSpacing: '0.05em', marginBottom: 10 }}>Active medications</div>
                        {patient.active_prescriptions?.length ? patient.active_prescriptions.map(rx => (
                            <div key={rx.id} style={{ padding: '8px 0', borderBottom: '1px solid var(--color-border)' }}>
                                <strong style={{ fontSize: 13 }}>{rx.drug_name} {rx.dose}</strong><br />
                                <span style={{ fontSize: 11, color: 'var(--color-text-2)' }}>{rx.frequency}</span>
                            </div>
                        )) : <div style={{ fontSize: 12, color: 'var(--color-text-2)' }}>None</div>}
                    </div>

                    <div style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 12, padding: 16 }}>
                        <div style={{ fontSize: 11, color: 'var(--color-text-2)', textTransform: 'uppercase', letterSpacing: '0.05em', marginBottom: 10 }}>Profile</div>
                        {[
                            ['Blood type', patient.blood_type],
                            ['Genotype', patient.genotype],
                            ['DOB', fmtDate(patient.dob)],
                            ['Gender', patient.gender ? patient.gender.charAt(0).toUpperCase() + patient.gender.slice(1) : null],
                        ].map(([k, v]) => (
                            <div key={k} style={{ display: 'flex', justifyContent: 'space-between', padding: '6px 0', borderBottom: '1px solid var(--color-border)', fontSize: 13 }}>
                                <span style={{ color: 'var(--color-text-2)' }}>{k}</span>
                                <span style={{ fontWeight: 600 }}>{v || '—'}</span>
                            </div>
                        ))}
                    </div>
                </aside>
            </div>
        </AppLayout>
    );
}

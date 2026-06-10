import { useEffect, useRef } from 'react';
import { Link } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';

function StatCard({ label, value, sub, icon, color }) {
    const bg = {
        green: { background: 'var(--color-green-lt)', borderTop: '3px solid var(--color-green)' },
        amber: { background: 'var(--color-amber-lt)', borderTop: '3px solid var(--color-amber)' },
        red:   { background: 'var(--color-red-lt)',   borderTop: '3px solid var(--color-red)' },
    }[color] ?? { background: 'var(--color-navy-lt)', borderTop: '3px solid var(--color-navy)' };

    return (
        <div style={{ ...bg, borderRadius: 12, padding: '18px 20px', position: 'relative', overflow: 'hidden' }}>
            <div style={{ fontSize: 11, fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.06em', color: 'var(--color-text-2)', marginBottom: 4 }}>{label}</div>
            <div style={{ fontSize: 28, fontWeight: 800, color: 'var(--color-navy)', lineHeight: 1.1 }}>{value}</div>
            <div style={{ fontSize: 12, color: 'var(--color-text-3)', marginTop: 3 }}>{sub}</div>
            <div style={{ position: 'absolute', right: 14, top: 14, fontSize: 22, opacity: 0.4 }}>{icon}</div>
        </div>
    );
}

function auditIcon(action = '') {
    if (action.includes('view')) return '👁';
    if (action.includes('break')) return '⚡';
    if (action.includes('dispense')) return '💊';
    if (action.includes('encounter')) return '📋';
    return '🔵';
}

function statusColor(status) {
    if (status === 'active') return { background: 'var(--color-red-lt)', color: 'var(--color-red)' };
    if (status === 'approved') return { background: 'var(--color-green-lt)', color: 'var(--color-green)' };
    if (status === 'disputed') return { background: 'var(--color-amber-lt)', color: 'var(--color-amber)' };
    return { background: '#F1F5F9', color: 'var(--color-text-3)' };
}

export default function Dashboard({ stats, recentLogs, breakGlass, charts }) {
    const regRef  = useRef(null);
    const roleRef = useRef(null);
    const encRef  = useRef(null);
    const dispRef = useRef(null);
    const chartsInstances = useRef([]);

    useEffect(() => {
        let cancelled = false;

        function loadCharts() {
            if (cancelled) return;
            if (!window.Chart) {
                const script = document.createElement('script');
                script.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js';
                script.onload = () => { if (!cancelled) buildCharts(); };
                document.head.appendChild(script);
            } else {
                buildCharts();
            }
        }

        function buildCharts() {
            // Destroy previous instances
            chartsInstances.current.forEach(c => c.destroy());
            chartsInstances.current = [];

            Chart.defaults.font = { family: '-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Arial,sans-serif', size: 11 };
            Chart.defaults.color = '#64748B';

            if (regRef.current) {
                chartsInstances.current.push(new Chart(regRef.current, {
                    type: 'line',
                    data: {
                        labels: charts.regLabels,
                        datasets: [{ label: 'Registrations', data: charts.regCounts, borderColor: '#1E3A5F', backgroundColor: 'rgba(30,58,95,0.07)', borderWidth: 2, pointRadius: 2, pointHoverRadius: 5, fill: true, tension: 0.4 }],
                    },
                    options: { responsive: true, plugins: { legend: { display: false } }, scales: { x: { grid: { display: false }, ticks: { maxTicksLimit: 10 } }, y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#F1F5F9' } } } },
                }));
            }

            if (roleRef.current) {
                chartsInstances.current.push(new Chart(roleRef.current, {
                    type: 'doughnut',
                    data: {
                        labels: charts.roleLabels,
                        datasets: [{ data: charts.roleValues, backgroundColor: ['#1E3A5F','#2C5F2D','#3B8C6E','#B8860B','#B23A48'], borderWidth: 2, borderColor: '#fff', hoverOffset: 6 }],
                    },
                    options: { responsive: true, cutout: '65%', plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, padding: 12, font: { size: 11 } } } } },
                }));
            }

            if (encRef.current) {
                chartsInstances.current.push(new Chart(encRef.current, {
                    type: 'bar',
                    data: {
                        labels: charts.encLabels,
                        datasets: [{ label: 'Encounters', data: charts.encCounts, backgroundColor: 'rgba(44,95,45,0.75)', borderRadius: 5, borderSkipped: false }],
                    },
                    options: { responsive: true, plugins: { legend: { display: false } }, scales: { x: { grid: { display: false } }, y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#F1F5F9' } } } },
                }));
            }

            if (dispRef.current) {
                chartsInstances.current.push(new Chart(dispRef.current, {
                    type: 'bar',
                    data: {
                        labels: charts.dispLabels,
                        datasets: [{ label: 'Dispenses', data: charts.dispCounts, backgroundColor: 'rgba(30,58,95,0.65)', borderRadius: 5, borderSkipped: false }],
                    },
                    options: { responsive: true, plugins: { legend: { display: false } }, scales: { x: { grid: { display: false } }, y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#F1F5F9' } } } },
                }));
            }
        }

        loadCharts();
        return () => { cancelled = true; chartsInstances.current.forEach(c => c.destroy()); chartsInstances.current = []; };
    }, [charts]);

    return (
        <AppLayout title="Dashboard">
            {/* Stats row */}
            <div style={{ display: 'grid', gridTemplateColumns: 'repeat(6,1fr)', gap: 16, marginBottom: 28 }}>
                <StatCard label="Patients"    value={stats.patients.toLocaleString()}    sub="Registered"      icon="👤" />
                <StatCard label="Providers"   value={stats.providers.toLocaleString()}   sub="Clinicians"      icon="🩺" color="green" />
                <StatCard label="Pharmacists" value={stats.pharmacists.toLocaleString()} sub="Dispensing"      icon="💊" color="green" />
                <StatCard label="Facilities"  value={stats.facilities.toLocaleString()}  sub="Registered"      icon="🏥" />
                <StatCard label="Break-glass" value={stats.break_glass}                  sub="Active events"   icon="⚡" color="amber" />
                <StatCard label="Total Users" value={stats.total_users.toLocaleString()} sub="All roles"       icon="🌐" />
            </div>

            {/* Charts row 1 */}
            <div style={{ display: 'grid', gridTemplateColumns: '2fr 1fr', gap: 20, marginBottom: 20 }}>
                <div style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 14, overflow: 'hidden' }}>
                    <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', padding: '16px 20px 12px' }}>
                        <div>
                            <div style={{ fontWeight: 700, fontSize: 14, color: 'var(--color-navy)' }}>New Registrations</div>
                            <div style={{ fontSize: 12, color: 'var(--color-text-2)', marginTop: 2 }}>Users registered per day — last 30 days</div>
                        </div>
                        <Link href="/admin/users" style={{ padding: '6px 14px', borderRadius: 8, border: '1.5px solid var(--color-border)', fontSize: 12, fontWeight: 600, color: 'var(--color-text-2)', textDecoration: 'none' }}>View Users</Link>
                    </div>
                    <div style={{ padding: '0 20px 16px' }}><canvas ref={regRef} height={90} /></div>
                </div>

                <div style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 14, overflow: 'hidden' }}>
                    <div style={{ padding: '16px 20px 12px' }}>
                        <div style={{ fontWeight: 700, fontSize: 14, color: 'var(--color-navy)' }}>User Roles</div>
                        <div style={{ fontSize: 12, color: 'var(--color-text-2)', marginTop: 2 }}>Distribution by role</div>
                    </div>
                    <div style={{ padding: '0 20px 16px', display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
                        <canvas ref={roleRef} height={200} style={{ maxHeight: 200 }} />
                    </div>
                </div>
            </div>

            {/* Charts row 2 */}
            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 20, marginBottom: 24 }}>
                <div style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 14 }}>
                    <div style={{ padding: '16px 20px 12px' }}>
                        <div style={{ fontWeight: 700, fontSize: 14, color: 'var(--color-navy)' }}>Clinical Encounters</div>
                        <div style={{ fontSize: 12, color: 'var(--color-text-2)', marginTop: 2 }}>Encounters per week — last 8 weeks</div>
                    </div>
                    <div style={{ padding: '0 20px 16px' }}><canvas ref={encRef} height={120} /></div>
                </div>

                <div style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 14 }}>
                    <div style={{ padding: '16px 20px 12px' }}>
                        <div style={{ fontWeight: 700, fontSize: 14, color: 'var(--color-navy)' }}>Pharmacy Dispenses</div>
                        <div style={{ fontSize: 12, color: 'var(--color-text-2)', marginTop: 2 }}>Prescriptions dispensed per month — last 6 months</div>
                    </div>
                    <div style={{ padding: '0 20px 16px' }}><canvas ref={dispRef} height={120} /></div>
                </div>
            </div>

            {/* Bottom row */}
            <div style={{ display: 'grid', gridTemplateColumns: '2fr 1fr', gap: 20 }}>
                {/* Audit log */}
                <div style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 14, overflow: 'hidden' }}>
                    <div style={{ padding: '16px 20px 12px', borderBottom: '1px solid var(--color-border)' }}>
                        <div style={{ fontWeight: 700, fontSize: 14, color: 'var(--color-navy)' }}>Recent Audit Events</div>
                        <div style={{ fontSize: 12, color: 'var(--color-text-2)', marginTop: 2 }}>Latest access and system events</div>
                    </div>
                    {recentLogs.length === 0 ? (
                        <div style={{ textAlign: 'center', padding: '32px', color: 'var(--color-text-3)' }}>
                            <div style={{ fontSize: 28, marginBottom: 8 }}>📋</div>
                            <p>No audit events yet.</p>
                        </div>
                    ) : recentLogs.map((log, i) => (
                        <div key={i} style={{ display: 'flex', alignItems: 'flex-start', gap: 12, padding: '12px 22px', borderBottom: '1px solid var(--color-border)', fontSize: 12 }}>
                            <div style={{ width: 32, height: 32, borderRadius: '50%', background: 'var(--color-navy-lt)', display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: 14, flexShrink: 0 }}>
                                {auditIcon(log.action)}
                            </div>
                            <div style={{ flex: 1, minWidth: 0 }}>
                                <div style={{ fontWeight: 600, color: 'var(--color-text)' }}>{log.actor_name ?? 'System'}</div>
                                <div style={{ color: 'var(--color-text-2)', marginTop: 1 }}>{(log.action ?? '').replace(/_/g, ' ')}</div>
                            </div>
                            <div style={{ color: 'var(--color-text-3)', whiteSpace: 'nowrap' }}>{log.created_at_human}</div>
                        </div>
                    ))}
                </div>

                {/* Break-glass */}
                <div style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 14, overflow: 'hidden' }}>
                    <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', padding: '16px 20px 12px', borderBottom: '1px solid var(--color-border)' }}>
                        <div>
                            <div style={{ fontWeight: 700, fontSize: 14, color: 'var(--color-red)' }}>⚡ Active Break-glass</div>
                            <div style={{ fontSize: 12, color: 'var(--color-text-2)', marginTop: 2 }}>Requires review</div>
                        </div>
                        <Link href="/admin/break-glass" style={{ padding: '6px 14px', borderRadius: 8, border: '1.5px solid var(--color-border)', fontSize: 12, fontWeight: 600, color: 'var(--color-text-2)', textDecoration: 'none' }}>All</Link>
                    </div>
                    {breakGlass.length === 0 ? (
                        <div style={{ textAlign: 'center', padding: '32px', color: 'var(--color-text-3)' }}>
                            <div style={{ fontSize: 28, marginBottom: 8 }}>✅</div>
                            <p>No active break-glass events.</p>
                        </div>
                    ) : breakGlass.map((event, i) => (
                        <div key={i} style={{ padding: '12px 22px', borderBottom: '1px solid var(--color-border)', fontSize: 12 }}>
                            <div style={{ fontWeight: 600, color: 'var(--color-text)' }}>{event.patient_name ?? 'Unknown patient'}</div>
                            <div style={{ color: 'var(--color-text-2)', marginTop: 2 }}>Dr. {event.provider_name ?? '—'}</div>
                            <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', marginTop: 6 }}>
                                <span style={{ ...statusColor(event.status), fontSize: 10, fontWeight: 700, padding: '2px 8px', borderRadius: 10, textTransform: 'capitalize' }}>{event.status}</span>
                                <span style={{ color: 'var(--color-text-3)' }}>{event.created_at_human}</span>
                            </div>
                        </div>
                    ))}
                </div>
            </div>
        </AppLayout>
    );
}

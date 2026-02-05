/**
 * Formatea una fecha para mostrar en la UI
 * @param {string} fecha - Fecha en formato ISO o YYYY-MM-DD
 * @returns {string} Fecha formateada (ej: "12 feb 2026")
 */
export const formatearFecha = (fecha) => {
    if (!fecha) return '';
    
    // Si viene en formato ISO (2026-02-09T00:00:00.000000Z), extraer solo la fecha
    let fechaLimpia = fecha;
    if (typeof fecha === 'string' && fecha.includes('T')) {
        fechaLimpia = fecha.split('T')[0];
    }
    
    // Parsear manualmente para evitar problemas de timezone
    const [year, month, day] = fechaLimpia.split('-').map(Number);
    const d = new Date(year, month - 1, day); // month-1 porque los meses en JS van de 0-11
    
    return d.toLocaleDateString('es-ES', { day: 'numeric', month: 'short', year: 'numeric' });
};

/**
 * Formatea una hora para mostrar en la UI
 * @param {string} hora - Hora en varios formatos
 * @returns {string} Hora formateada (ej: "09:35")
 */
export const formatearHora = (hora) => {
    if (!hora) return '';
    
    // Si es una fecha ISO completa (2026-02-04T19:00:00.000000Z)
    if (hora.includes('T')) {
        const d = new Date(hora);
        return d.toLocaleTimeString('es-ES', { 
            hour: '2-digit', 
            minute: '2-digit',
            hour12: false 
        });
    }
    
    // Si viene con día de la semana: "JUEVES 09:35:00.0000000"
    if (hora.includes(' ')) {
        const partes = hora.split(' ');
        const soloHora = partes[1] || partes[0];
        return soloHora.substring(0, 5);
    }
    
    // Si ya es formato HH:MM o HH:MM:SS, retornar solo HH:MM
    return hora.substring(0, 5);
};

/**
 * Formatea una fecha para usar en input type="date"
 * @param {string} fecha - Fecha en varios formatos
 * @returns {string} Fecha en formato YYYY-MM-DD
 */
export const formatearFechaParaInput = (fecha) => {
    if (!fecha) return '';
    
    // Si ya viene en formato YYYY-MM-DD, retornar directamente
    if (typeof fecha === 'string' && /^\d{4}-\d{2}-\d{2}$/.test(fecha)) {
        return fecha;
    }
    
    // Si viene en formato ISO (2026-02-09T00:00:00.000000Z), extraer solo la fecha
    if (typeof fecha === 'string' && fecha.includes('T')) {
        return fecha.split('T')[0];
    }
    
    // Si es otro formato, convertir
    const d = new Date(fecha);
    const year = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
};

/**
 * Formatea una hora para usar en input type="time"
 * @param {string} hora - Hora en varios formatos
 * @returns {string} Hora en formato HH:MM
 */
export const formatearHoraParaInput = (hora) => {
    if (!hora) return '';
    
    // Si es una fecha ISO completa (2026-02-04T19:00:00.000000Z)
    if (hora.includes('T')) {
        const d = new Date(hora);
        const hours = String(d.getHours()).padStart(2, '0');
        const minutes = String(d.getMinutes()).padStart(2, '0');
        return `${hours}:${minutes}`;
    }
    
    // Si viene con día de la semana: "JUEVES 09:35:00.0000000"
    if (hora.includes(' ')) {
        const partes = hora.split(' ');
        const soloHora = partes[1] || partes[0];
        return soloHora.substring(0, 5);
    }
    
    // Si ya es formato HH:MM o HH:MM:SS
    return hora.substring(0, 5);
};

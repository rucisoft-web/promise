// Grafik dasbor yang bisa diklik: setiap dataset boleh membawa `urls` (satu URL per titik data)
window.filamentChartJsPlugins ??= []
window.filamentChartJsPlugins.push({
    id: 'promiseLinks',
    afterEvent(chart, args) {
        const e = args.event
        if (! chart.data.datasets.some((d) => d.urls)) return
        if (e.type !== 'click' && e.type !== 'mousemove') return
        const el = chart.getElementsAtEventForMode(e.native ?? e, 'nearest', { intersect: true }, false)[0]
        const url = el ? chart.data.datasets[el.datasetIndex]?.urls?.[el.index] : null
        chart.canvas.style.cursor = url ? 'pointer' : ''
        if (e.type === 'click' && url) window.location.href = url
    },
})

// Format angka Indonesia (60.000; 1,25) pada sumbu dan tooltip
window.filamentChartJsPlugins.push({
    id: 'promiseLocale',
    beforeInit(chart) {
        chart.options.locale = 'id-ID'
    },
})

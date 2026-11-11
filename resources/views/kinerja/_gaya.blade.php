<style>
    /* Bintang hanya tampil (rata-rata) */
    .st-tampil {
        display: inline-block;
        font-size: 1.15rem;
        line-height: 1;
        letter-spacing: 1px;
        background: linear-gradient(90deg, #f59e0b calc(var(--nilai) / 5 * 100%), #d5dbe5 0);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
    }

    /* Bintang yang bisa dipilih (CSS murni) */
    .st-pilih { position: relative; display: inline-flex; flex-direction: row-reverse; }
    .st-pilih input { position: absolute; width: 1px; height: 1px; opacity: 0; }
    .st-pilih label { padding: 0 .1rem; font-size: 1.7rem; line-height: 1; color: #cbd5e1; cursor: pointer; transition: color .1s; }
    .st-pilih input:checked ~ label,
    .st-pilih label:hover,
    .st-pilih label:hover ~ label { color: #f59e0b; }
    .st-pilih input:focus-visible + label { outline: 2px solid #2563eb; border-radius: 4px; }
</style>

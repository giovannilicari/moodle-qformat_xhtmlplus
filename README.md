# XHTML+ Question Export Format - Complete Solution

## Overview

L'esportatore **XHTML+ (xhtmlplus)** è stato completamente migliorato per supportare **TUTTE** le informazioni richieste per ogni domanda nel deposito domande di Moodle.

## Informazioni Esportate

Per ogni domanda, il formato XHTML+ esporta:

### 1. **Metadati della Domanda**
- ✅ ID domanda
- ✅ Nome domanda
- ✅ Tipo di domanda (qtype)
- ✅ Punteggio predefinito (defaultmark)

### 2. **Testo della Domanda**
- ✅ Testo della domanda formattato
- ✅ Formattazione preservata

### 3. **Risposte Corrette**
- ✅ Tutte le risposte possibili
- ✅ Indicazione visiva (✔) delle risposte corrette
- ✅ Frazione/peso della risposta

### 4. **Feedback**
- ✅ Feedback generale della domanda
- ✅ Feedback specifico per ogni risposta

### 5. **Tag**
- ✅ Associati alla domanda (quando disponibili)

### 6. **Punteggio**
- ✅ Punteggio predefinito
- ✅ Frazione per ogni risposta (per domande a scelta multipla)

## Tipi di Domande Supportati

L'esportatore XHTML+ supporta **tutti** i seguenti tipi di domande:

1. ✅ **Vero/Falso** (truefalse)
2. ✅ **Scelta Multipla** (multichoice) - risposta singola e multipla
3. ✅ **Risposta Breve** (shortanswer)
4. ✅ **Numerica** (numerical) - con tolleranza
5. ✅ **Abbinamento** (match)
6. ✅ **Essay/Scritto** (essay)
7. ✅ **Risposta Multipla (Cloze)** (multianswer)
8. ✅ **Calcolata** (calculated, calculatedmulti, calculatedsimple)
9. ✅ **Drag and Drop** (ddimageortext, ddmarker, ddwtos)
10. ✅ **Seleziona da Vuoto** (gapselect)
11. ✅ **Descrizione** (description)

## Struttura del File HTML Esportato

### Intestazione
```html
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Exported Questions with Complete Solutions</title>
  <style>/* CSS completo incluso */</style>
</head>
<body>
```

### Contenuto (per ogni domanda)
```html
<!-- Metadati -->
<div class="question-metadata">
  <div class="meta-row">
    <span class="meta-label">ID:</span>
    <span class="meta-value">42</span>
  </div>
  <!-- ... altri metadati ... -->
</div>

<!-- Testo domanda -->
<div class="question-text">
  <h3>Nome Domanda</h3>
  <div class="questiontext">Testo della domanda...</div>
</div>

<!-- Contenuto (risposte, feedback) -->
<div class="question-content">
  <!-- Dipende dal tipo di domanda -->
</div>

<!-- Feedback generale -->
<div class="general-feedback">
  <strong>Feedback generale:</strong>
  <div class="feedback-content">...</div>
</div>
```

## Stili CSS Inclusi

Il file CSS esportato include stili per:

- **Evidenziazione risposte corrette**: sfondo verde e badge ✔
- **Feedback**: box blu per feedback specifici
- **Metadati**: sezione grigiastra per informazioni
- **Design responsivo**: perfetto per mobile e desktop
- **Stampa**: ottimizzato per stampa su carta

### Colori Utilizzati
- 🟢 Verde (#27ae60): risposte corrette
- 🔵 Blu (#3498db): informazioni e feedback
- 🟡 Giallo (#ffc107): dati numerici

## Esempio di Utilizzo

### Come esportare le domande:

1. Vai alla **Banca delle Domande** di Moodle
2. Seleziona la **categoria** o le **domande** da esportare
3. Scegli il formato **"XHTML+ format with complete solutions"**
4. Clicca **"Esporta"**

Il file HTML risultante conterrà:
- ✅ Tutte le domande con metadati
- ✅ Tutte le risposte e il feedback
- ✅ Visuale professionale e stampabile
- ✅ Completamente autonomo (no dipendenze da Moodle)

## Vantaggi

| Vantaggi | Descrizione |
|----------|------------|
| 📋 **Completo** | Tutte le informazioni richieste |
| 🎨 **Visuale** | Design moderno e professionale |
| 📱 **Responsive** | Funziona su mobile e desktop |
| 🖨️ **Stampabile** | Perfetto per stampa |
| 🔍 **Leggibile** | Facile da leggere e comprendere |
| 🔒 **Autonomo** | Non necessita di connessione a Moodle |

## File Modificati

```
question/format/xhtmlplus/
├── format.php              ✅ Migliorato con supporto completo
├── xhtml.css               ✅ CSS moderno e responsivo
└── lang/en/
    └── qformat_xhtmlplus.php ✅ Stringhe aggiornate
```

Inoltre aggiornato:
```
question/format/xhtml/
├── format.php              ✅ Versione parallela migliora
├── xhtml.css               ✅ CSS completo
└── lang/en/
    └── qformat_xhtml.php   ✅ Stringhe aggiornate
```

## Caratteristiche Specifiche per Tipo di Domanda

### Vero/Falso
```
✔ Risposta: Vero
  Feedback: ...
```

### Scelta Multipla
```
✔ Risposta 1 (corretta)
   Feedback specifico
☐ Risposta 2
   Feedback specifico
```

### Risposta Breve
```
Input text: _________________
✔ Risposte corrette: "Giusta1" | "Giusta2"
Feedback: ...
```

### Numerica
```
Input text: _________________
✔ Risposta: 42 ±0.5
Feedback: ...
```

### Abbinamento
```
Domanda 1 → ✔ Risposta corretta 1
Domanda 2 → ✔ Risposta corretta 2
```

### Essay
```
Tipo di risposta: HTML Editor
Risposta richiesta: Sì/No
Allegati: numero consentito
```

### Multianswer (Cloze)
```
Primo buco: ✔ risposta corretta
Secondo buco: ✔ risposta corretta
Feedback per ogni buco
```

## Validazione

Tutti i file sono stati validati:
- ✅ Sintassi PHP corretta
- ✅ HTML valido
- ✅ CSS conforme agli standard

## Compatibilità

- ✅ Moodle 3.x e superiori
- ✅ Tutti i browser moderni
- ✅ Mobile-friendly
- ✅ Stampa supportata

## Note Importanti

1. **Locale**: Tutte le stringhe usano il sistema di localizzazione di Moodle
2. **Sicurezza**: HTML è escapato per prevenire XSS
3. **Performance**: CSS è inline nel documento per autonomia
4. **Accessibilità**: Struttura HTML semantica e ben formattata

---

**Versione**: 1.0.0
**Ultimo aggiornamento**: 2026

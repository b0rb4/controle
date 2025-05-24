<?php
// manager.php
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Gerenciador de Eventos</title>
    <link rel="stylesheet" href="styles.css" />
</head>
<body>
    <div class="container" id="page-manager" style="display: block;">
        <div class="header">
            <h1>⏱️ Gerenciador de Eventos</h1>
            <p>Organize e execute seus eventos com cronômetro automático</p>
        </div>

        <div class="main-content">
            <div class="timer-section">
                <div class="timer-display" id="timerDisplay">00:00:00</div>
                
                <div class="current-event" id="currentEvent">
                    <h3>Nenhum evento ativo</h3>
                    <p>Adicione eventos e clique em "Iniciar" para começar</p>
                </div>

                <div class="controls">
                    <button class="btn btn-success" id="startBtn">▶️ Iniciar</button>
                    <button class="btn btn-warning" id="pauseBtn" disabled>⏸️ Pausar</button>
                    <button class="btn btn-danger" id="stopBtn" disabled>⏹️ Parar</button>
                    <button class="btn btn-primary" id="nextBtn" disabled>⏭️ Próximo</button>
                </div>

                <div class="progress-bar">
                    <div class="progress-fill" id="progressFill" style="width: 0%"></div>
                </div>
            </div>

            <div class="events-section">
                <div class="add-event-form">
                    <h3 style="margin-bottom: 20px; color: #ddd;">➕ Adicionar Evento</h3>
                    
                    <div class="form-group">
                        <label>Horário do Evento</label>
                        <div class="time-inputs">
                            <input type="number" id="eventHour" placeholder="Hora" min="0" max="23" value="0" />
                            <input type="number" id="eventMinute" placeholder="Minuto" min="0" max="59" value="0" />
                            <input type="number" id="eventSecond" placeholder="Segundo" min="0" max="59" value="0" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Descrição do Evento</label>
                        <input type="text" id="eventDescription" placeholder="Ex: cam1, apresentação, intervalo..." value="" />
                    </div>

                    <div class="form-group">
                        <label>Duração do Evento</label>
                        <div class="duration-inputs">
                            <input type="number" id="durationMinutes" placeholder="Minutos" min="0" value="1" />
                            <input type="number" id="durationSeconds" placeholder="Segundos" min="0" max="59" value="30" />
                        </div>
                    </div>

                    <button class="btn btn-primary" id="addEventBtn">Adicionar Evento</button>
                </div>

                <div class="events-list" id="eventsList">
                    <p style="text-align: center; color: #a29bfe; padding: 20px;">
                        Nenhum evento adicionado ainda.<br />
                        Use o formulário acima para adicionar eventos.
                    </p>
                </div>
            </div>
        </div>
    </div>

<script>
class EventManager {
    constructor() {
        this.events = [];
        this.currentEventIndex = -1;
        this.isRunning = false;
        this.isPaused = false;
        this.timer = null;
        this.currentTime = 0;
        this.eventStartTime = 0;
        this.eventDuration = 0;
        this.editingEventId = null;
        
        this.initializeElements();
        this.bindEvents();
        this.updateDisplay();
    }

    initializeElements() {
        this.timerDisplay = document.getElementById('timerDisplay');
        this.currentEvent = document.getElementById('currentEvent');
        this.eventsList = document.getElementById('eventsList');
        this.progressFill = document.getElementById('progressFill');
        
        this.startBtn = document.getElementById('startBtn');
        this.pauseBtn = document.getElementById('pauseBtn');
        this.stopBtn = document.getElementById('stopBtn');
        this.nextBtn = document.getElementById('nextBtn');
        this.addEventBtn = document.getElementById('addEventBtn');
    }

    bindEvents() {
        this.startBtn.addEventListener('click', () => this.start());
        this.pauseBtn.addEventListener('click', () => this.pause());
        this.stopBtn.addEventListener('click', () => this.stop());
        this.nextBtn.addEventListener('click', () => this.nextEvent());
        this.addEventBtn.addEventListener('click', () => this.addEvent());
    }

    addEvent() {
        const hour = parseInt(document.getElementById('eventHour').value) || 0;
        const minute = parseInt(document.getElementById('eventMinute').value) || 0;
        const second = parseInt(document.getElementById('eventSecond').value) || 0;
        const description = document.getElementById('eventDescription').value.trim();
        const durationMinutes = parseInt(document.getElementById('durationMinutes').value) || 0;
        const durationSeconds = parseInt(document.getElementById('durationSeconds').value) || 0;

        if (!description) {
            alert('Por favor, adicione uma descrição para o evento');
            return;
        }

        const event = {
            id: Date.now(),
            hour,
            minute,
            second,
            description,
            durationMinutes,
            durationSeconds,
            totalDurationSeconds: (durationMinutes * 60) + durationSeconds,
            completed: false
        };

        this.events.push(event);
        this.clearForm();
        this.renderEvents();
        this.updateDisplay();
    }

    clearForm() {
        document.getElementById('eventHour').value = '0';
        document.getElementById('eventMinute').value = '0';
        document.getElementById('eventSecond').value = '0';
        document.getElementById('eventDescription').value = '';
        document.getElementById('durationMinutes').value = '1';
        document.getElementById('durationSeconds').value = '30';
    }

    removeEvent(id) {
        this.events = this.events.filter(event => event.id !== id);
        this.editingEventId = null;
        this.renderEvents();
        this.updateDisplay();
    }

    editEvent(id) {
        this.editingEventId = this.editingEventId === id ? null : id;
        this.renderEvents();
    }

    saveEvent(id) {
        const event = this.events.find(e => e.id === id);
        if (!event) return;

        const hour = parseInt(document.getElementById(`edit-hour-${id}`).value) || 0;
        const minute = parseInt(document.getElementById(`edit-minute-${id}`).value) || 0;
        const second = parseInt(document.getElementById(`edit-second-${id}`).value) || 0;
        const description = document.getElementById(`edit-description-${id}`).value.trim();
        const durationMinutes = parseInt(document.getElementById(`edit-duration-minutes-${id}`).value) || 0;
        const durationSeconds = parseInt(document.getElementById(`edit-duration-seconds-${id}`).value) || 0;

        if (!description) {
            alert('Por favor, adicione uma descrição para o evento');
            return;
        }

        event.hour = hour;
        event.minute = minute;
        event.second = second;
        event.description = description;
        event.durationMinutes = durationMinutes;
        event.durationSeconds = durationSeconds;
        event.totalDurationSeconds = (durationMinutes * 60) + durationSeconds;

        this.editingEventId = null;
        this.renderEvents();
        this.updateDisplay();
    }

    cancelEdit() {
        this.editingEventId = null;
        this.renderEvents();
    }

    moveEvent(id, direction) {
        const index = this.events.findIndex(event => event.id === id);
        if (index === -1) return;

        const newIndex = direction === 'up' ? index - 1 : index + 1;
        if (newIndex < 0 || newIndex >= this.events.length) return;

        [this.events[index], this.events[newIndex]] = [this.events[newIndex], this.events[index]];
        this.renderEvents();
    }

    renderEvents() {
        if (this.events.length === 0) {
            this.eventsList.innerHTML = `
                <p style="text-align: center; color: #a29bfe; padding: 20px;">
                    Nenhum evento adicionado ainda.<br />
                    Use o formulário acima para adicionar eventos.
                </p>`;
            return;
        }

        let html = '';
        this.events.forEach((event, index) => {
            const isActive = index === this.currentEventIndex;
            const isCompleted = event.completed;
            const isEditing = this.editingEventId === event.id;
            
            html += `
                <div class="event-item ${isActive ? 'active' : ''} ${isCompleted ? 'completed' : ''}" data-id="${event.id}">
                    <div class="event-header">
                        <div class="event-time">
                            ${String(event.hour).padStart(2, '0')}:${String(event.minute).padStart(2, '0')}:${String(event.second).padStart(2, '0')}
                        </div>
                        <div class="event-actions">
                            <button class="btn btn-primary btn-small" onclick="eventManager.moveEvent(${event.id}, 'up')" ${index === 0 ? 'disabled' : ''}>↑</button>
                            <button class="btn btn-primary btn-small" onclick="eventManager.moveEvent(${event.id}, 'down')" ${index === this.events.length - 1 ? 'disabled' : ''}>↓</button>
                            <button class="btn btn-warning btn-small" onclick="eventManager.editEvent(${event.id})">✏️</button>
                            <button class="btn btn-danger btn-small" onclick="eventManager.removeEvent(${event.id})">🗑️</button>
                        </div>
                    </div>
                    <div class="event-description">${event.description}</div>
                    <div class="event-duration">Duração: ${event.durationMinutes}m ${event.durationSeconds}s</div>
            `;
        
            if (isEditing) {
                html += `
                    <div class="edit-form">
                        <div class="form-group">
                            <label>Horário do Evento</label>
                            <div class="time-inputs">
                                <input type="number" id="edit-hour-${event.id}" placeholder="Hora" min="0" max="23" value="${event.hour}" />
                                <input type="number" id="edit-minute-${event.id}" placeholder="Minuto" min="0" max="59" value="${event.minute}" />
                                <input type="number" id="edit-second-${event.id}" placeholder="Segundo" min="0" max="59" value="${event.second}" />
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Descrição</label>
                            <input type="text" id="edit-description-${event.id}" value="${event.description}" />
                        </div>
                        
                        <div class="form-group">
                            <label>Duração</label>
                            <div class="duration-inputs">
                                <input type="number" id="edit-duration-minutes-${event.id}" placeholder="Minutos" min="0" value="${event.durationMinutes}" />
                                <input type="number" id="edit-duration-seconds-${event.id}" placeholder="Segundos" min="0" max="59" value="${event.durationSeconds}" />
                            </div>
                        </div>
                        
                        <button class="btn btn-success btn-small" onclick="eventManager.saveEvent(${event.id})">Salvar</button>
                        <button class="btn btn-secondary btn-small" onclick="eventManager.cancelEdit()">Cancelar</button>
                    </div>
                `;
            }

            html += `</div>`;
        });

        this.eventsList.innerHTML = html;
    }

    updateDisplay() {
        if (this.currentEventIndex === -1 || !this.events[this.currentEventIndex]) {
            this.currentEvent.innerHTML = `
                <h3>Nenhum evento ativo</h3>
                <p>Adicione eventos e clique em "Iniciar" para começar</p>`;
            this.timerDisplay.textContent = '00:00:00';
            this.progressFill.style.width = '0%';

            this.startBtn.disabled = this.events.length === 0;
            this.pauseBtn.disabled = true;
            this.stopBtn.disabled = true;
            this.nextBtn.disabled = true;
            return;
        }

        const event = this.events[this.currentEventIndex];
        this.currentEvent.innerHTML = `
            <h3>Evento atual</h3>
            <p><strong>Descrição:</strong> ${event.description}</p>
            <p><strong>Horário:</strong> ${String(event.hour).padStart(2, '0')}:${String(event.minute).padStart(2, '0')}:${String(event.second).padStart(2, '0')}</p>
            <p><strong>Duração:</strong> ${event.durationMinutes}m ${event.durationSeconds}s</p>
        `;

        // Disable start if already running
        this.startBtn.disabled = this.isRunning && !this.isPaused;
        this.pauseBtn.disabled = !this.isRunning || this.isPaused;
        this.stopBtn.disabled = !this.isRunning;
        this.nextBtn.disabled = !this.isRunning;
    }

    start() {
        if (this.isRunning && !this.isPaused) return;

        if (this.currentEventIndex === -1) {
            if (this.events.length === 0) {
                alert('Nenhum evento para iniciar');
                return;
            }
            this.currentEventIndex = 0;
        }

        const event = this.events[this.currentEventIndex];

        if (!this.isPaused) {
            // Start fresh
            this.currentTime = 0;
            this.eventDuration = event.totalDurationSeconds;
            this.eventStartTime = Date.now();
        } else {
            // Resume from pause
            this.eventStartTime = Date.now() - this.currentTime * 1000;
        }

        this.isRunning = true;
        this.isPaused = false;
        this.updateDisplay();

        this.timer = setInterval(() => {
            const elapsed = Math.floor((Date.now() - this.eventStartTime) / 1000);
            this.currentTime = elapsed;

            if (this.currentTime >= this.eventDuration) {
                this.completeCurrentEvent();
            } else {
                this.updateTimerDisplay(this.eventDuration - this.currentTime);
                this.updateProgressBar(this.currentTime, this.eventDuration);
            }
        }, 250);
    }

    pause() {
        if (!this.isRunning || this.isPaused) return;

        this.isPaused = true;
        clearInterval(this.timer);
        this.updateDisplay();
    }

    stop() {
        if (!this.isRunning) return;

        clearInterval(this.timer);
        this.isRunning = false;
        this.isPaused = false;
        this.currentTime = 0;
        this.updateTimerDisplay(this.events[this.currentEventIndex].totalDurationSeconds);
        this.updateProgressBar(0, this.events[this.currentEventIndex].totalDurationSeconds);
        this.updateDisplay();
    }

    nextEvent() {
        if (this.currentEventIndex === -1) return;

        this.completeCurrentEvent(true);
    }

    completeCurrentEvent(skipAlert = false) {
        clearInterval(this.timer);
        this.events[this.currentEventIndex].completed = true;

        if (!skipAlert) {
            alert(`Evento "${this.events[this.currentEventIndex].description}" concluído!`);
        }

        if (this.currentEventIndex + 1 < this.events.length) {
            this.currentEventIndex++;
            this.isRunning = false;
            this.isPaused = false;
            this.currentTime = 0;
            this.start();
        } else {
            alert('Todos os eventos foram concluídos!');
            this.isRunning = false;
            this.isPaused = false;
            this.currentEventIndex = -1;
            this.currentTime = 0;
            this.updateTimerDisplay(0);
            this.updateProgressBar(0, 1);
            this.updateDisplay();
        }

        this.renderEvents();
    }

    updateTimerDisplay(seconds) {
        const h = Math.floor(seconds / 3600);
        const m = Math.floor((seconds % 3600) / 60);
        const s = seconds % 60;
        this.timerDisplay.textContent = 
            `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
    }

    updateProgressBar(elapsed, total) {
        const percent = (elapsed / total) * 100;
        this.progressFill.style.width = `${percent}%`;
    }
}

const eventManager = new EventManager();
</script>
</body>
</html>

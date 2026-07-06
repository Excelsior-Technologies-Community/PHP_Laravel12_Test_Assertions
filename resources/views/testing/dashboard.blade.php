<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advanced Testing Control Center</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-950 text-gray-100 min-h-screen">
    <div class="max-w-6xl mx-auto p-6">
        <div class="mb-8 border-b border-gray-800 pb-5">
            <h1 class="text-3xl font-black text-indigo-400">🧪 Testing & Assertions Ecosystem</h1>
            <p class="text-gray-400 mt-1">Live Automation Framework • Runtime Assertion Matrix</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <div class="bg-gray-900 border border-gray-800 p-5 rounded-2xl flex flex-col justify-between shadow-xl">
                <div>
                    <span class="text-xs uppercase tracking-wider text-indigo-400 font-bold block mb-1">Module 01</span>
                    <h3 class="text-lg font-bold text-gray-200">Architecture Asserter</h3>
                    <p class="text-xs text-gray-400 mt-1">Validates directory separation layer. Ensures controllers are isolated from row db schemas.</p>
                </div>
                <div class="mt-4 flex gap-2 items-center">
                    <button onclick="triggerTest('architecture')" class="bg-indigo-600 hover:bg-indigo-500 transition text-xs font-bold px-3 py-2 rounded-xl text-white">Execute Test</button>
                    <span id="badge-architecture" class="text-[10px] uppercase font-black tracking-widest text-gray-500 bg-gray-800/50 px-2 py-1 rounded-md">Idle</span>
                </div>
            </div>

            <div class="bg-gray-900 border border-gray-800 p-5 rounded-2xl flex flex-col justify-between shadow-xl">
                <div>
                    <span class="text-xs uppercase tracking-wider text-indigo-400 font-bold block mb-1">Module 02</span>
                    <h3 class="text-lg font-bold text-gray-200">JSON Schema Struct</h3>
                    <p class="text-xs text-gray-400 mt-1">Asserts API object types mapping constraints, ensuring node types stay strict.</p>
                </div>
                <div class="mt-4 flex gap-2 items-center">
                    <button onclick="triggerTest('jsonschema')" class="bg-indigo-600 hover:bg-indigo-500 transition text-xs font-bold px-3 py-2 rounded-xl text-white">Execute Test</button>
                    <span id="badge-jsonschema" class="text-[10px] uppercase font-black tracking-widest text-gray-500 bg-gray-800/50 px-2 py-1 rounded-md">Idle</span>
                </div>
            </div>

            <div class="bg-gray-900 border border-gray-800 p-5 rounded-2xl flex flex-col justify-between shadow-xl">
                <div>
                    <span class="text-xs uppercase tracking-wider text-indigo-400 font-bold block mb-1">Module 03</span>
                    <h3 class="text-lg font-bold text-gray-200">Database & Audit Tracker</h3>
                    <p class="text-xs text-gray-400 mt-1">Simulates creation, logs mutation arrays, soft deletes model rows, verifying lifecycle integrity.</p>
                </div>
                <div class="mt-4 flex gap-2 items-center">
                    <button onclick="triggerTest('audit')" class="bg-indigo-600 hover:bg-indigo-500 transition text-xs font-bold px-3 py-2 rounded-xl text-white">Execute Test</button>
                    <span id="badge-audit" class="text-[10px] uppercase font-black tracking-widest text-gray-500 bg-gray-800/50 px-2 py-1 rounded-md">Idle</span>
                </div>
            </div>

            <div class="bg-gray-900 border border-gray-800 p-5 rounded-2xl flex flex-col justify-between shadow-xl">
                <div>
                    <span class="text-xs uppercase tracking-wider text-indigo-400 font-bold block mb-1">Module 04</span>
                    <h3 class="text-lg font-bold text-gray-200">Form Request Validation</h3>
                    <p class="text-xs text-gray-400 mt-1">Injects corrupted payloads to ensure request state parameters bounce back.</p>
                </div>
                <div class="mt-4 flex gap-2 items-center">
                    <button onclick="triggerTest('validation')" class="bg-indigo-600 hover:bg-indigo-500 transition text-xs font-bold px-3 py-2 rounded-xl text-white">Execute Test</button>
                    <span id="badge-validation" class="text-[10px] uppercase font-black tracking-widest text-gray-500 bg-gray-800/50 px-2 py-1 rounded-md">Idle</span>
                </div>
            </div>

            <div class="bg-gray-900 border border-gray-800 p-5 rounded-2xl flex flex-col justify-between shadow-xl">
                <div>
                    <span class="text-xs uppercase tracking-wider text-indigo-400 font-bold block mb-1">Module 05</span>
                    <h3 class="text-lg font-bold text-gray-200">HTTP Logistics Mock</h3>
                    <p class="text-xs text-gray-400 mt-1">Fakes external client boundaries using HTTP response mocking mechanics.</p>
                </div>
                <div class="mt-4 flex gap-2 items-center">
                    <button onclick="triggerTest('httpmock')" class="bg-indigo-600 hover:bg-indigo-500 transition text-xs font-bold px-3 py-2 rounded-xl text-white">Execute Test</button>
                    <span id="badge-httpmock" class="text-[10px] uppercase font-black tracking-widest text-gray-500 bg-gray-800/50 px-2 py-1 rounded-md">Idle</span>
                </div>
            </div>
        </div>

        <div class="bg-gray-900 border border-gray-800 rounded-2xl p-6 shadow-2xl">
            <h3 class="text-lg font-bold text-gray-200 mb-4">Runtime Logs Output Console</h3>
            <div id="consoleLog" class="bg-gray-950 border border-gray-800 p-4 rounded-xl font-mono text-xs text-emerald-400 h-64 overflow-y-auto whitespace-pre-wrap">System components ready for orchestration. Inbound assertions trace output stream will appear below...</div>
        </div>
    </div>

    <script>
        function triggerTest(type) {
            const consoleLog = document.getElementById('consoleLog');
            const badge = document.getElementById('badge-' + type);
            
            badge.className = "text-[10px] uppercase font-black tracking-widest text-amber-400 bg-amber-500/10 px-2 py-1 rounded-md";
            badge.innerText = "Running";
            
            consoleLog.innerText += `\n[${new Date().toLocaleTimeString()}] Triggering ${type.toUpperCase()} assertion sequence...`;

            fetch(`/testing/run/${type}`)
                .then(res => res.json())
                .then(data => {
                    consoleLog.innerText += `\n[LOG]: ${data.message}`;
                    if(data.errors_caught) consoleLog.innerText += `\n[ERRORS EXPOSED]: ${JSON.stringify(data.errors_caught)}`;
                    if(data.schema) consoleLog.innerText += `\n[PAYLOAD SCHEMA]: ${JSON.stringify(data.schema)}`;
                    if(data.mocked_response) consoleLog.innerText += `\n[MOCKED BACKEND]: ${JSON.stringify(data.mocked_response)}`;
                    
                    if (data.status === 'passed') {
                        badge.className = "text-[10px] uppercase font-black tracking-widest text-emerald-400 bg-emerald-500/10 px-2 py-1 rounded-md";
                        badge.innerText = "Passed";
                    } else {
                        badge.className = "text-[10px] uppercase font-black tracking-widest text-rose-400 bg-rose-500/10 px-2 py-1 rounded-md";
                        badge.innerText = "Failed";
                    }
                    consoleLog.scrollTop = consoleLog.scrollHeight;
                })
                .catch(err => {
                    badge.className = "text-[10px] uppercase font-black tracking-widest text-rose-400 bg-rose-500/10 px-2 py-1 rounded-md";
                    badge.innerText = "Error";
                    consoleLog.innerText += `\n[CRITICAL ERROR]: Pipeline execution broken.`;
                });
        }
    </script>
</body>
</html>
module.exports = {
    apps: [{
        name: "batb-api",
        script: "php",
        args: "artisan octane:start --server=roadrunner --port=20120 --host=0.0.0.0", // Use your assigned port!
        instances: 1,
        autorestart: true,
        watch: false,
        max_memory_restart: "1G"
    }]
};
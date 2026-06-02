/** @type {import('vite').UserConfig} */
const ddevOrigin = process.env.DDEV_PRIMARY_URL_WITHOUT_PORT;

export default {
    server: {
        host: ddevOrigin ? "0.0.0.0" : "127.0.0.1",
        port: 5174,
        strictPort: true,
        ...(ddevOrigin
            ? {
                  origin: `${ddevOrigin}:5174`,
                  cors: {
                      origin: /https?:\/\/([A-Za-z0-9\-\.]+)?(\.ddev\.site)(?::\d+)?$/,
                  },
              }
            : {}),
    },
};

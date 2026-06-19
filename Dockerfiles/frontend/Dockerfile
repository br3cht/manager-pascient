FROM node:20-alpine

ARG uid=1000

WORKDIR /app

COPY package.json package-lock.json* ./
RUN npm ci

COPY . .

USER ${uid}

CMD ["sh", "-c", "npm run build:standalone"]

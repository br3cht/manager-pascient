FROM node:20-alpine

WORKDIR /app

COPY package.json package-lock.json* ./
RUN npm ci

COPY . .

USER 1000

CMD ["sh", "-c", "npm run build:standalone"]

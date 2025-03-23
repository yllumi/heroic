const path = require('path');
const TerserPlugin = require('terser-webpack-plugin');

module.exports = (env, argv) => ({
  entry: './src/index.js',
  output: {
    filename: argv.mode === 'development' ? 'heroic.dev.js' : 'heroic.min.js',
    path: path.resolve(__dirname, 'dist'),
  },
  mode: argv.mode || 'production',
  devtool: argv.mode === 'development' ? 'source-map' : false,
  optimization: {
    minimize: argv.mode === 'production',
    minimizer: [new TerserPlugin()],
  },
});

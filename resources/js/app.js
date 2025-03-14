import './bootstrap';
import 'flowbite'

import 'handsontable/dist/handsontable.full.min.css';
import { registerAllModules } from 'handsontable/registry';
import Handsontable from 'handsontable';

// Register Handsontable's modules
registerAllModules();

// Make Handsontable available globally
window.Handsontable = Handsontable;
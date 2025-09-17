import { Component, Inject } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogModule } from '@angular/material/dialog';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-document-details-dialog',
  standalone: true,
  imports: [CommonModule, MatDialogModule],
  templateUrl: './details-dialogue.component.html',
})
export class DetailsDialogueComponent {
  constructor(@Inject(MAT_DIALOG_DATA) public data: any) {}
}

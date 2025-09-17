import { Component, Input } from '@angular/core';
import { MatDialog, MatDialogModule } from '@angular/material/dialog';
import { MatSnackBar, MatSnackBarModule } from '@angular/material/snack-bar';
import { MatCardModule } from '@angular/material/card';
import { MatButtonModule } from '@angular/material/button';
import { CommonModule } from '@angular/common';
import { HttpClient } from '@angular/common/http';
import { DetailsDialogueComponent } from './details/details-dialogue.component';
import { DeleteDialog } from './delete/delete-dialogue.component';
import { MatIconModule } from '@angular/material/icon';

@Component({
  selector: 'app-document-list',
  standalone: true,
  imports: [
    CommonModule,
    MatCardModule,
    MatDialogModule,
    MatButtonModule,
    MatSnackBarModule,
    MatIconModule,
    DetailsDialogueComponent
  ],
  templateUrl: './document-list.component.html',
  styleUrls: ['./document-list.component.scss']
})
export class DocumentListComponent {
  @Input() documents: any[] = [];

  constructor(
    private dialog: MatDialog,
    private snackBar: MatSnackBar,
    private http: HttpClient
  ) {}

  openDocumentDialog(doc: any) {
    this.dialog.open(DetailsDialogueComponent, {
      width: '500px',
      data: doc
    });
  }

  confirmDelete(doc: any) {
    const dialogRef = this.dialog.open(DeleteDialog, {
      width: '300px',
      data: { filename: doc.filename }
    });

    dialogRef.afterClosed().subscribe(result => {
      if (result === true) {
        this.deleteDocument(doc);
      }
    });
  }

  deleteDocument(doc: any) {
    this.http.delete(`http://localhost:8080/api/documents/?id=${doc.id}`).subscribe({
      next: () => {
        this.documents = this.documents.filter(d => d.id !== doc.id);
        this.snackBar.open('File deleted successfully', 'Close', { duration: 3000 });
      },
      error: () => {
        this.snackBar.open('Failed to delete file', 'Close', { duration: 3000 });
      }
    });
  }
}

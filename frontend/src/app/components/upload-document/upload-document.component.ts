import { Component, EventEmitter, Output } from '@angular/core';
import { CommonModule } from '@angular/common';
import { HttpClient } from '@angular/common/http';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-upload-document',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './upload-document.component.html',
  styleUrl: './upload-document.component.scss'
})
export class UploadDocumentComponent {
  @Output() uploadComplete = new EventEmitter<void>();
  selectedFile: File | null = null;
  dragOver = false;
  uploadStatus = '';

  constructor(private http: HttpClient) {}

  onFileSelected(event: Event) {
    const input = event.target as HTMLInputElement;
    if (input.files && input.files.length > 0) {
      this.selectedFile = input.files[0];
      this.uploadStatus = '';
    }
  }

  onDragOver(event: DragEvent) {
    event.preventDefault();
    this.dragOver = true;
  }

  onDragLeave(event: DragEvent) {
    event.preventDefault();
    this.dragOver = false;
  }

  onDrop(event: DragEvent) {
    event.preventDefault();
    this.dragOver = false;
    if (event.dataTransfer?.files.length) {
      this.selectedFile = event.dataTransfer.files[0];
      this.uploadStatus = '';
    }
  }

  onUpload() {
    if (!this.selectedFile) {
      this.uploadStatus = 'Please select a file first.';
      return;
    }

    const formData = new FormData();
    // backend expects 'document' or 'file' — use what your API expects
    formData.append('document', this.selectedFile);

    this.http.post('http://localhost:8080/api/documents/', formData).subscribe({
      next: () => {
        this.uploadStatus = '✅ File uploaded successfully!';
        this.selectedFile = null;
        this.uploadComplete.emit();

        setTimeout(() => {
          this.uploadStatus = '';
        }, 3000);
      },
      error: (err) => {
        console.error(err);
        this.uploadStatus = '❌ Upload failed. Check console for details.';
      }
    });
  }
}

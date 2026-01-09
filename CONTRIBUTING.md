# Contributing to Reservasi

Thank you for your interest in contributing to the Nuca Beauty Skin booking system!

## How to contribute

1. **Fork the repository**
   - Click the "Fork" button at the top right of the repository page

2. **Clone your fork**
   ```bat
   git clone https://github.com/YOUR_USERNAME/Reservasi.git
   cd Reservasi
   ```

3. **Create a new branch**
   ```bat
   git checkout -b feature/your-feature-name
   ```

4. **Make your changes**
   - Write clean, readable code
   - Follow Laravel and Vue.js best practices
   - Keep the pink/magenta theme consistent
   - Add comments for complex logic

5. **Test your changes**
   ```bat
   php artisan test
   ```
   - Ensure all existing tests pass
   - Add new tests for new functionality

6. **Commit your changes**
   ```bat
   git add .
   git commit -m "Add: description of your changes"
   ```
   
   Use clear commit messages:
   - `Add:` for new features
   - `Fix:` for bug fixes
   - `Update:` for improvements
   - `Remove:` for deletions

7. **Push to your fork**
   ```bat
   git push origin feature/your-feature-name
   ```

8. **Open a Pull Request**
   - Go to the original repository
   - Click "New Pull Request"
   - Select your branch
   - Describe your changes clearly

## Guidelines

### Code style

- Follow PSR-12 coding standards for PHP
- Use Tailwind CSS utility classes (avoid custom CSS when possible)
- Keep Vue components small and focused
- Use meaningful variable and function names

### Testing

- Write tests for new features and bug fixes
- Ensure test coverage doesn't decrease
- Test manually in the browser before submitting

### UI/UX

- Maintain the pink/magenta theme (`#EC4899` to `#9333EA` gradients)
- Ensure responsive design works on mobile and desktop
- Test with different screen sizes

### Database migrations

- Never modify existing migrations that have been deployed
- Create new migrations for schema changes
- Test migration rollback (`php artisan migrate:rollback`)

### Documentation

- Update README.md if you change setup steps
- Add docblocks to new functions and classes
- Comment complex business logic

## Reporting bugs

If you find a bug, please create an issue with:

1. **Description** - Clear explanation of the bug
2. **Steps to reproduce** - How to trigger the bug
3. **Expected behavior** - What should happen
4. **Actual behavior** - What actually happens
5. **Environment** - PHP version, Laravel version, OS
6. **Screenshots** - If applicable

## Feature requests

For new features, create an issue with:

1. **Use case** - Why this feature is needed
2. **Proposed solution** - How it should work
3. **Alternatives** - Other approaches you considered
4. **Additional context** - Screenshots, mockups, etc.

## Questions?

If you have questions about contributing, feel free to:
- Open an issue with the `question` label
- Check existing issues for similar questions

## Code of Conduct

- Be respectful and professional
- Welcome newcomers and help them learn
- Focus on constructive feedback
- Keep discussions on-topic

Thank you for helping improve the Reservasi booking system! 🎀
